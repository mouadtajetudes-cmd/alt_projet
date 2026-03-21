<?php
namespace alt\api\middlewares;

use App\core\Application\Provider\jwt\JwtManager;
use App\core\Application\useCase\AuthzService;
use App\core\Application\DTO\ProfileDTO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthzMiddleware {

    private AuthzService $authzservice;
    private JwtManager $authnService;

    public function __construct(
        AuthzService $authzservice, 
        JwtManager $authnService
    ){
        $this->authzservice = $authzservice;
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {

        $token = $request->getHeaderLine('Authorization');
        if (!$token) {
            return $this->forbiddenResponse('Token manquant');
        }

        try {
            $profile = $this->authnService->validate($token);

            if (!$profile instanceof ProfileDTO) {
                return $this->forbiddenResponse('Token invalide');
            }

            $request = $request->withAttribute('profile', $profile);

            $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
            $route = $routeContext->getRoute();

            if (!$route) {
                return $this->forbiddenResponse("Route non trouvée");
            }

            $routeName = $route->getName() ?? '';
            $args = $route->getArguments();

            switch($routeName){

                case 'agenda_pratien':
                    $praticienId = $args['id'] ?? null;
                    if (!$this->authzservice->canAccessAgenda($profile, $praticienId)) {
                        return $this->forbiddenResponse('Accès à l’agenda refusé');
                    }
                    break;

                case 'rdv_detail':
                    $rdvId = $args['id'] ?? null;
                    $rdv = $this->rdv->findById($rdvId);

                    if (!$rdv || !$this->authzservice->canAccessRendezVous($profile, $rdv)) {
                        return $this->forbiddenResponse('Accès au rendez-vous refusé');
                    }
                    break;

                default:
                    break;
            }

            return $handler->handle($request);

        } catch (\Exception $e) {
            return $this->forbiddenResponse('Erreur autorisation : ' . $e->getMessage());
        }
    }

    private function forbiddenResponse(string $message): Response {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }
}

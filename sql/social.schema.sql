-- table:posts
CREATE TABLE posts ( 
id_post SERIAL PRIMARY KEY, 
titre VARCHAR(255) NOT NULL, 
description TEXT, 
date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
id_utilisateur INTEGER NOT NULL, 
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);
-- table:medias
CREATE TABLE medias ( 
id_media SERIAL PRIMARY KEY, 
titre VARCHAR(255)
);
-- table:post_medias
CREATE TABLE post_medias ( 
id_media INTEGER, 
id_post INTEGER, 
PRIMARY KEY (id_media, id_post), 
FOREIGN KEY (id_media) REFERENCES medias(id_media), 
FOREIGN KEY (id_post) REFERENCES posts(id_post) 
); 
--table:commentaires
CREATE TABLE commentaires ( 
id_commentaire SERIAL PRIMARY KEY, 
details TEXT NOT NULL, 
id_utilisateur INTEGER NOT NULL, 
id_post INTEGER NOT NULL, 
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
FOREIGN KEY (id_post) REFERENCES posts(id_post) ,
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)


);
-- table:reactions
CREATE TABLE reactions ( 
id_reaction SERIAL PRIMARY KEY, 
type VARCHAR(50) NOT NULL, 
id_utilisateur INTEGER NOT NULL, 
id_post INTEGER NOT NULL, 
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
FOREIGN KEY (id_post) REFERENCES posts(id_post), 
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur), 
UNIQUE(id_utilisateur, id_post) 
);


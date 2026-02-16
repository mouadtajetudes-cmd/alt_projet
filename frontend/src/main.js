import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/css/style.css'
<<<<<<< HEAD
=======
import './style.css'

// FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
  faHome, faComments, faUsers, faUserCircle, faSignInAlt, faUserPlus, 
  faPencilAlt, faTrash, faCrown, faGem, faEnvelope, faLock, faSearch,
  faBars, faTimes, faChevronDown, faPhone, faInfoCircle, faPaperPlane,
  faSmile, faPaperclip, faCheck, faSignOutAlt, faEdit, faPlus, faUser,
  faLayerGroup, faUserMinus, faCalendar, faCamera, faImage, faCircle,
  faInbox, faHeart, faThumbsUp, faReply, faFileAlt, faDownload, faEye
} from '@fortawesome/free-solid-svg-icons'

library.add(
  faHome, faComments, faUsers, faUserCircle, faSignInAlt, faUserPlus,
  faPencilAlt, faTrash, faCrown, faGem, faEnvelope, faLock, faSearch,
  faBars, faTimes, faChevronDown, faPhone, faInfoCircle, faPaperPlane,
  faSmile, faPaperclip, faCheck, faSignOutAlt, faEdit, faPlus, faUser,
  faLayerGroup, faUserMinus, faCalendar, faCamera, faImage, faCircle,
  faInbox, faHeart, faThumbsUp, faReply, faFileAlt, faDownload, faEye
)
>>>>>>> origin/groupe/develop

const app = createApp(App)
const pinia = createPinia()

<<<<<<< HEAD
=======
app.component('font-awesome-icon', FontAwesomeIcon)
>>>>>>> origin/groupe/develop
app.use(pinia)
app.use(router)
app.mount('#app')

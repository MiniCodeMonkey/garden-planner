import './bootstrap';
import { createApp } from 'vue'
import Navigation from './components/Navigation.vue'
import Calendar from './components/Calendar.vue'

createApp(Navigation).mount("#navigation")
createApp(Calendar).mount("#calendar")

import './bootstrap';
import { createApp } from 'vue';

// 1. Import our new component
import TaskList from './components/TaskList.vue';

// 2. Create the Vue application instance
const app = createApp({});

// 3. Register our component globally
app.component('task-list', TaskList);

// 4. Mount the app to the DOM element with the ID 'app'
app.mount('#app');

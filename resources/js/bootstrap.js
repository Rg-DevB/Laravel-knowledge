import axios from 'axios';
import csrf from './csrf.js';

axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf();

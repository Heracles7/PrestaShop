import Vue from 'vue';
import app from './components/app';
import store from './store/';
import router from './router';
import Translation from './mixins/translate';

Vue.mixin(Translation);

new Vue({
  router,
  store,
  el: '#translations-app',
  template: '<app />',
  components: { app },
  beforeMount() {
    this.$store.dispatch('getTranslations');
  }
});

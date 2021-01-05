import Vue from 'vue';
import Api from "./api";

import vSelect from 'vue-select';

import './components/Fields/fields.js';

import Field from "./components/Field";
import Button from "./components/Button";
import Tabs from "./components/Tabs/Tabs";
import Tab from "./components/Tabs/Tab";
import MetaBox from "./components/MetaBox";
import DateQueryField from "./components/DateQueryField";
import TaxQueryField from "./components/TaxQueryField";
import MetaQueryField from "./components/MetaQueryField";
import Synonyms from "./components/Synonyms";
import StopWords from "./components/StopWords";
import RankingRules from "./components/RankingRules";
import SearchMetaBox from "./components/SearchMetaBox";
import Popup from "./components/Popup";
import AjaxSyncPopup from "./components/AjaxSyncPopup";

import SettingsView from './views/Settings';
import IndexView from './views/Index';
import IndexesView from './views/Indexes';

import {translate} from "./filters/translate";

Vue.component('v-select', vSelect);

Vue.component('mp-field', Field);
Vue.component('mp-button', Button);
Vue.component('mp-tabs', Tabs);
Vue.component('mp-tab', Tab);
Vue.component('mp-meta-box', MetaBox);
Vue.component('mp-date-query-field', DateQueryField);
Vue.component('mp-tax-query-field', TaxQueryField);
Vue.component('mp-meta-query-field', MetaQueryField);
Vue.component('mp-synonyms', Synonyms);
Vue.component('mp-stop-words', StopWords);
Vue.component('mp-ranking-rules', RankingRules);
Vue.component('mp-search-meta-box', SearchMetaBox);
Vue.component('mp-popup', Popup);
Vue.component('mp-ajax-sync-popup', AjaxSyncPopup);

Vue.component('mp-settings-view', SettingsView);
Vue.component('mp-index-view', IndexView);
Vue.component('mp-indexes-view', IndexesView);

Vue.filter('__', translate);
Vue.prototype.$trans = translate;
Vue.prototype.$api = Api;

const app = new Vue({

    data: {
        messages: {}
    },

    created() {

    },

    mounted() {

    },

    methods: {
        addMessage(id, message, type, dismissible = true) {
            this.$set(this.messages, id, {
                ...message,
                dismissible,
                type
            });
        },

        successMessage(id, message, dismissible = true) {
            this.addMessage(id, message, 'success', dismissible);
        },

        errorMessage(id, message, dismissible = true) {
            this.addMessage(id, message, 'error', dismissible);
        },

        warningMessage(id, message, dismissible = true) {
            this.addMessage(id, message, 'warning', dismissible);
        },

        infoMessage(id, message, dismissible = true) {
            this.addMessage(id, message, 'info', dismissible);
        },

        dismissMessage(id) {
            this.$delete(this.messages, id);
        },

        clearMessages() {
            this.messages = {};
        },

        syncPopup() {
            return this.$refs.syncPopup;
        }

    },

    components: {
    }

});

app.$mount('#meilipress-app');

if(! window.MeiliPress) {
    window.MeiliPress = {};
}

window.MeiliPress.app = app;
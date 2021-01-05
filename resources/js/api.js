const $ = window.jQuery;

class Api {

    constructor() {
        this.stopReindex = false;
    }

    async post(action, data = {}) {
        action = 'meilipress/'+action;

        let res,
            security = MeiliPress.security[action];

        try {
            res = await $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action,
                    security,
                    data: JSON.stringify(data)
                }
            });
        }
        catch (e) {
            if(e.status === 422) {
                return e.responseJSON;
            }

            if(! e.responseJSON) {
                MeiliPress.app.errorMessage(action+'_unknown', {
                    content: "Unknown error"
                });

                return null;
            }

            const {data} = e.responseJSON;

            if(data.content) {
                MeiliPress.app.errorMessage(action, data, true);
            }

            return null;
        }

        if(! res || res === '0') {
            MeiliPress.app.errorMessage(action+'_unknown', {
                content: "Unknown error"
            });

            return null;
        }

        return res;
    }

    async reindex(indexId) {
        this.stopReindex = false;

        const body = {index: indexId};
        const app = MeiliPress.app;
        const stateData = await this.post('indexes/state', body);

        if(! stateData.success) {
            return;
        }

        if(stateData.data.type !== 'ajax') {
            const res = await this.post('indexes/reindex', body);
            return app.successMessage('reindex_cron', res.data.content);
        }

        let state = stateData.data.state;
        app.syncPopup().show(stateData.data.indexName, stateData.data.state);

        while (state && ! this.stopReindex) {
            app.syncPopup().loadingStart();
            let res = await this.post('indexes/reindex', body);
            state = res.data;

            if(state.total <=state.current) {
                state = false;
                break;
            }

            app.syncPopup().update(state);
        }

        app.syncPopup().hide();
    }

    cancelReindex() {
        this.stopReindex = true;
    }

    async deleteIndex(indexId) {

    }

    async changeIndexSyncStatus(indexId) {

    }



}

export default new Api();
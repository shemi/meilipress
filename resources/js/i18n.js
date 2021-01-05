import _ from 'underscore';

export default function (key, replacements = {}) {
    let trans = MeiliPress.i18n[key] || key || '';

    for (let replace of Object.keys(replacements)) {
        trans = trans.replace(new RegExp(`\{${replace}\}`, 'g'), replacements[replace]);
    }

    return _.unescape(trans);
}

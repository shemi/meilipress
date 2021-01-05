import __ from '../i18n';

export function translate(key, replace = {})
{
    if (! key) {
        return "";
    }

    return __(key, replace);
}

const AREA_OTHER = "other";

let permissionConfig = { permissions: false, allowed: {} };

/**
 * Takes the config of the "sulu_ai_translator" key (see AITranslatorAdmin::getConfig()).
 */
export const setPermissionConfig = (config) => {
    permissionConfig = { permissions: false, allowed: {}, ...config };
};

/**
 * Areas are resource keys with their own permission. All other resource keys share the area "other".
 */
export const isTranslationAllowed = (resourceKey) => {
    const { permissions, allowed } = permissionConfig;

    if (!permissions) {
        return true;
    }

    const area = Object.prototype.hasOwnProperty.call(allowed, resourceKey)
        ? resourceKey
        : AREA_OTHER;

    return allowed[area] === true;
};

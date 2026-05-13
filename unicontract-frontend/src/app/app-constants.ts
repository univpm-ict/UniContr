import { appConfig } from './app.config';

export class AppConstants {
    public static get baseURL(): string { return appConfig.api.baseURL; }
    public static get apiVer(): string { return appConfig.api.apiVer; }

    public static get baseApiURL(): string { return this.baseURL + this.apiVer; }
    public static get documentationURL(): string { return appConfig.api.documentationURL; }
    public static get allowedEmailDomains(): string[] { return appConfig.email.allowedDomains; }
    public static get corsiAltaQualificazione(): string[] { return appConfig.contractTypes.corsiAltaQualificazione; }
    public static get corsiUfficiali(): string[] { return appConfig.contractTypes.corsiUfficiali; }
    public static get corsiIntegrativi(): string[] { return appConfig.contractTypes.corsiIntegrativi; }
    public static get corsiSupporto(): string[] { return appConfig.contractTypes.corsiSupporto; }
}


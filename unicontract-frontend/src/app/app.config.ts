import { environment } from '../environments/environment';

export const appConfig = {
  api: {
    baseURL: environment.API_URL,
    apiVer: 'api/v1',
    documentationURL: environment.documentation,
  },
  email: {
    allowedDomains: ['univpm.it'],
  },
  contractTypes: {
    corsiAltaQualificazione: ['ALTQG', 'ALTQC', 'ALTQU'],
    corsiUfficiali: ['CONTC', 'CONTU', 'CONTR'],
    corsiIntegrativi: ['INTC', 'INTU', 'INTXU', 'INTXC'],
    corsiSupporto: ['SUPPU', 'SUPPC'],
  },
};

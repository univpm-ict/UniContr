import { AppConstants } from 'src/app/app-constants';

export class EmailHelper {
  public static allowedDomains(): string[] {
    return (AppConstants.allowedEmailDomains || [])
      .map((domain) => domain.toLowerCase().trim().replace(/^@/, ''))
      .filter((domain) => domain.length > 0);
  }

  public static hasAllowedDomain(email?: string | null): boolean {
    if (!email) {
      return false;
    }

    const normalizedEmail = email.toLowerCase().trim();
    const atPosition = normalizedEmail.lastIndexOf('@');

    if (atPosition < 0 || atPosition === normalizedEmail.length - 1) {
      return false;
    }

    const domain = normalizedEmail.substring(atPosition + 1);
    return this.allowedDomains().includes(domain);
  }

  public static isInstitutionalPlaceholder(email?: string | null): boolean {
    if (!email) {
      return false;
    }

    const normalizedEmail = email.toLowerCase().trim();

    return this.allowedDomains().some((domain) =>
      normalizedEmail === `*@${domain}` || normalizedEmail === `%@${domain}%`
    );
  }
}

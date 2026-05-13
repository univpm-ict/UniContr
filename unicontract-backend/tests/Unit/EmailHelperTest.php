<?php

namespace Tests\Unit;

use App\Service\EmailHelper;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EmailHelperTest extends TestCase
{
    public function test_has_allowed_domain_accepts_configured_domains(): void
    {
        Config::set('unidem.allowed_email_domains', [
            'uniurb.it',
            ' @univpm.it ',
        ]);

        $this->assertTrue(EmailHelper::hasAllowedDomain('docente@uniurb.it'));
        $this->assertTrue(EmailHelper::hasAllowedDomain('DOCENTE@UNIVPM.IT'));
    }

    public function test_has_allowed_domain_rejects_invalid_or_unconfigured_domains(): void
    {
        Config::set('unidem.allowed_email_domains', ['uniurb.it']);

        $this->assertFalse(EmailHelper::hasAllowedDomain('docente@example.com'));
        $this->assertFalse(EmailHelper::hasAllowedDomain('docente'));
        $this->assertFalse(EmailHelper::hasAllowedDomain(null));
    }
}

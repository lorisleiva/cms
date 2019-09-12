<?php namespace Tests;

use Statamic\Facades\Config;
use Statamic\Sites\Site;

class ConfigTest extends TestCase
{
    /** @test */
    function gets_config_var()
    {
        config(['foo' => 'bar']);

        $this->assertEquals('bar', Config::get('foo'));
        $this->assertEquals('fallback', Config::get('nonexistant_variable', 'fallback'));
    }

    /** @test */
    function gets_scoped_var()
    {
        config(['myscope' => ['baz' => 'qux']]);

        $this->assertEquals('qux', Config::get('myscope.baz'));
    }

    /** @test */
    function sets_var()
    {
        $this->assertEquals('doesnt exist', Config::get('foo', 'doesnt exist'));

        Config::set('foo', 'bar');

        $this->assertEquals('bar', Config::get('foo'));
    }

    /** @test */
    function gets_all_variables()
    {
        $this->app->instance('config', new \Illuminate\Config\Repository(['foo' => 'bar']));

        $this->assertEquals(['foo' => 'bar'], Config::all());
    }

    /** @test */
    function gets_app_key()
    {
        config(['app.key' => '123']);

        $this->assertEquals('123', Config::getAppKey());
    }

    /** @test */
    function gets_license_key()
    {
        config(['statamic.system.license_key' => '123']);
        $this->assertEquals('123', Config::getLicenseKey());

        config(['statamic.system.license_key' => '']);
        $this->assertNull(Config::getLicenseKey());

        config(['statamic.system.license_key' => null]);
        $this->assertNull(Config::getLicenseKey());
    }

    /** @test */
    function gets_site()
    {
        $this->fakeSiteConfig();

        tap(Config::getSite('en'), function ($site) {
            $this->assertInstanceOf(Site::class, $site);
            $this->assertEquals('en', $site->handle());
        });
    }

    /** @test */
    function gets_full_locale()
    {
        $this->fakeSiteConfig();

        $this->assertEquals('en_US', Config::getFullLocale());
        $this->assertEquals('en_US', Config::getFullLocale('en'));
        $this->assertEquals('fr_FR', Config::getFullLocale('fr'));
        $this->assertEquals('de_DE', Config::getFullLocale('de'));
    }

    /** @test */
    function gets_short_locale()
    {
        $this->fakeSiteConfig();

        $this->assertEquals('en', Config::getShortLocale());
        $this->assertEquals('en', Config::getShortLocale('en'));
        $this->assertEquals('fr', Config::getShortLocale('fr'));
        $this->assertEquals('de', Config::getShortLocale('de'));
    }

    /** @test */
    function gets_locale_name()
    {
        $this->fakeSiteConfig();

        $this->assertEquals('English', Config::getLocaleName());
        $this->assertEquals('English', Config::getLocaleName('en'));
        $this->assertEquals('French', Config::getLocaleName('fr'));
        $this->assertEquals('German', Config::getLocaleName('de'));
    }

    /** @test */
    function gets_locale_handles()
    {
        $this->fakeSiteConfig();

        $this->assertEquals(['en', 'fr', 'de'], Config::getLocales());
    }

    /** @test */
    function gets_default_locale()
    {
        $this->fakeSiteConfig();

        $this->assertEquals('en', Config::getDefaultLocale());
    }

    /** @test */
    function gets_other_locale_handles()
    {
        $this->fakeSiteConfig();

        $this->assertEquals(['fr', 'de'], Config::getOtherLocales());
    }

    /** @test */
    function gets_site_url()
    {
        $this->fakeSiteConfig();

        $this->assertEquals('http://test.com/', Config::getSiteUrl());
        $this->assertEquals('http://test.com/', Config::getSiteUrl('en'));
        $this->assertEquals('http://fr.test.com/', Config::getSiteUrl('fr'));
        $this->assertEquals('http://test.com/de/', Config::getSiteUrl('de'));
    }

    /** @test */
    function gets_routes()
    {
        $routes = [
            'collection' => ['blog' => '/blog/{slug}'],
            'routes' => ['/foo' => 'bar']
        ];

        config(['statamic.routes' => $routes]);

        $this->assertEquals($routes, Config::getRoutes());
    }

    /** @test */
    function gets_image_manipulation_presets()
    {
        $presets = [
            'small' => ['w' => 100],
            'large' => ['w' => 1000],
        ];

        config(['statamic.assets.image_manipulation.presets' => $presets]);

        $this->assertEquals($presets, Config::getImageManipulationPresets());
    }

    private function fakeSiteConfig()
    {
        \Statamic\Facades\Site::setConfig([
            'default' => 'en',
            'sites' => [
                'en' => ['name' => 'English', 'locale' => 'en_US', 'url' => 'http://test.com/'],
                'fr' => ['name' => 'French', 'locale' => 'fr_FR', 'url' => 'http://fr.test.com/'],
                'de' => ['name' => 'German', 'locale' => 'de_DE', 'url' => 'http://test.com/de/']
            ]
        ]);
    }
}
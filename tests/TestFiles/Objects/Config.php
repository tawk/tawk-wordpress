<?php

namespace Tawk\Tests\TestFiles\Objects;


class Config {
	public TawkConfig $tawk;
	public SeleniumConfig $selenium;
	public BrowserStackConfig $browserstack;
	public WebConfig $web;
}

class TawkConfig {
	public string $username;
	public string $password;
	public string $property_id;
	public string $widget_id;
	public string $embed_url;
}

class UrlConfig {
	public string $host;
	public string $port;
	public bool $https_flag = false;
}

class WebUserConfig {
	public string $username;
	public string $password;
	public string $name;
	public string $email;
}

class WebConfig {
	public UrlConfig $url;
	public WebUserConfig $admin;
}

class SeleniumConfig {
	public string $browser;
	public bool $hub_flag;
	public UrlConfig $url;
}

class BrowserStackConfig {
	public ?string $username;
	public ?string $access_key;
	public ?string $local_identifier;
	public ?string $build_name;
	public ?string $project_name;
	public int $session_timeout_ms;
	public int $request_timeout_ms;
	public bool $is_browserstack;
}

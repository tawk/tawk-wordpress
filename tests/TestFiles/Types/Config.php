<?php

namespace Tawk\Tests\TestFiles\Types;

class Config {
	public TawkConfig $tawk;
	public SeleniumConfig $selenium;
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

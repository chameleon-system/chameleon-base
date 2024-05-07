<?php

namespace ChameleonSystem\CoreBundle\Service;

class FontAwesomeService implements FontAwesomeServiceInterface
{
    public function filterFontAwesomeClasses(array $cssClassNames): array
    {
        if (false === $this->hasFontAwesomeClass($cssClassNames)) {
            return $cssClassNames;
        }

        $filteredCssClassNames = [];

        foreach($cssClassNames as $cssClassName) {
            if (true === \str_contains($cssClassName, ':before')) {
                $prefix = '.fas';
                if ($this->isFontAwesomeBrand($cssClassName)) {
                    $prefix = '.fab';
                }
                $filteredCssClassNames[] = $prefix.' '.$cssClassName;
            }
        }

        return $filteredCssClassNames;
    }

    private function hasFontAwesomeClass(array $cssClassNames): bool
    {
        return \in_array('.fa', $cssClassNames, true);
    }    
    
    private function isFontAwesomeBrand(string $cssClass): bool
    {
        $cssClass = \str_replace([':before', '.'],'', $cssClass);

        $fontAwesome5brands = [
            "fa-zhihu",
            "fa-youtube-square",
            "fa-youtube",
            "fa-yoast",
            "fa-yelp",
            "fa-yarn",
            "fa-yandex-international",
            "fa-yandex",
            "fa-yammer",
            "fa-yahoo",
            "fa-y-combinator",
            "fa-xing-square",
            "fa-xing",
            "fa-xbox",
            "fa-wpressr",
            "fa-wpforms",
            "fa-wpexplorer",
            "fa-wpbeginner",
            "fa-wordpress-simple",
            "fa-wordpress",
            "fa-wolf-pack-battalion",
            "fa-wodu",
            "fa-wizards-of-the-coast",
            "fa-wix",
            "fa-windows",
            "fa-wikipedia-w",
            "fa-whmcs",
            "fa-whatsapp-square",
            "fa-whatsapp",
            "fa-weixin",
            "fa-weibo",
            "fa-weebly",
            "fa-waze",
            "fa-watchman-monitoring",
            "fa-vuejs",
            "fa-vnv",
            "fa-vk",
            "fa-vine",
            "fa-vimeo-v",
            "fa-vimeo-square",
            "fa-vimeo",
            "fa-viber",
            "fa-viadeo-square",
            "fa-viadeo",
            "fa-viacoin",
            "fa-vaadin",
            "fa-ussunnah",
            "fa-usps",
            "fa-usb",
            "fa-ups",
            "fa-untappd",
            "fa-unsplash",
            "fa-unity",
            "fa-uniregistry",
            "fa-uncharted",
            "fa-umbraco",
            "fa-uikit",
            "fa-ubuntu",
            "fa-uber",
            "fa-typo3",
            "fa-twitter-square",
            "fa-twitter",
            "fa-twitch",
            "fa-tumblr-square",
            "fa-tumblr",
            "fa-trello",
            "fa-trade-federation",
            "fa-tiktok",
            "fa-think-peaks",
            "fa-themeisle",
            "fa-themeco",
            "fa-the-red-yeti",
            "fa-tencent-weibo",
            "fa-telegram-plane",
            "fa-telegram",
            "fa-teamspeak",
            "fa-symfony",
            "fa-swift",
            "fa-suse",
            "fa-supple",
            "fa-superpowers",
            "fa-stumbleupon-circle",
            "fa-stumbleupon",
            "fa-studiovinari",
            "fa-stripe-s",
            "fa-stripe",
            "fa-strava",
            "fa-sticker-mule",
            "fa-steam-symbol",
            "fa-steam-square",
            "fa-steam",
            "fa-staylinked",
            "fa-stackpath",
            "fa-stack-overflow",
            "fa-stack-exchange",
            "fa-squarespace",
            "fa-spotify",
            "fa-speaker-deck",
            "fa-speakap",
            "fa-sourcetree",
            "fa-soundcloud",
            "fa-snapchat-square",
            "fa-snapchat-ghost",
            "fa-snapchat",
            "fa-slideshare",
            "fa-slack-hash",
            "fa-slack",
            "fa-skype",
            "fa-skyatlas",
            "fa-sketch",
            "fa-sith",
            "fa-sistrix",
            "fa-simplybuilt",
            "fa-shopware",
            "fa-shopify",
            "fa-shirtsinbulk",
            "fa-servicestack",
            "fa-sellsy",
            "fa-sellcast",
            "fa-searchengin",
            "fa-scribd",
            "fa-schlix",
            "fa-sass",
            "fa-salesforce",
            "fa-safari",
            "fa-rust",
            "fa-rockrms",
            "fa-rocketchat",
            "fa-rev",
            "fa-resolving",
            "fa-researchgate",
            "fa-replyd",
            "fa-renren",
            "fa-redhat",
            "fa-reddit-square",
            "fa-reddit-alien",
            "fa-reddit",
            "fa-red-river",
            "fa-rebel",
            "fa-readme",
            "fa-reacteurope",
            "fa-react",
            "fa-ravelry",
            "fa-raspberry-pi",
            "fa-r-project",
            "fa-quora",
            "fa-quinscape",
            "fa-qq",
            "fa-python",
            "fa-pushed",
            "fa-product-hunt",
            "fa-playstation",
            "fa-pinterest-square",
            "fa-pinterest-p",
            "fa-pinterest",
            "fa-pied-piper-square",
            "fa-pied-piper-pp",
            "fa-pied-piper-hat",
            "fa-pied-piper-alt",
            "fa-pied-piper",
            "fa-php",
            "fa-phoenix-squadron",
            "fa-phoenix-framework",
            "fa-phabricator",
            "fa-periscope",
            "fa-perbyte",
            "fa-penny-arcade",
            "fa-paypal",
            "fa-patreon",
            "fa-palfed",
            "fa-pagelines",
            "fa-page4",
            "fa-osi",
            "fa-orcid",
            "fa-optin-monster",
            "fa-opera",
            "fa-openid",
            "fa-opencart",
            "fa-old-republic",
            "fa-odnoklassniki-square",
            "fa-odnoklassniki",
            "fa-octopus-deploy",
            "fa-nutritionix",
            "fa-ns8",
            "fa-npm",
            "fa-node-js",
            "fa-node",
            "fa-nimblr",
            "fa-neos",
            "fa-napster",
            "fa-monero",
            "fa-modx",
            "fa-mizuni",
            "fa-mixer",
            "fa-mixcloud",
            "fa-mix",
            "fa-microsoft",
            "fa-microblog",
            "fa-mendeley",
            "fa-megaport",
            "fa-meetup",
            "fa-medrt",
            "fa-medium-m",
            "fa-medium",
            "fa-medapps",
            "fa-mdb",
            "fa-maxcdn",
            "fa-mastodon",
            "fa-markdown",
            "fa-mandalorian",
            "fa-mailchimp",
            "fa-magento",
            "fa-lyft",
            "fa-linux",
            "fa-linode",
            "fa-linkedin-in",
            "fa-linkedin",
            "fa-line",
            "fa-less",
            "fa-leanpub",
            "fa-lastfm-square",
            "fa-lastfm",
            "fa-laravel",
            "fa-korvue",
            "fa-kickstarter-k",
            "fa-kickstarter",
            "fa-keycdn",
            "fa-keybase",
            "fa-kaggle",
            "fa-jsfiddle",
            "fa-js-square",
            "fa-js",
            "fa-joomla",
            "fa-joget",
            "fa-jira",
            "fa-jenkins",
            "fa-jedi-order",
            "fa-java",
            "fa-itunes-note",
            "fa-itunes",
            "fa-itch-io",
            "fa-ioxhost",
            "fa-invision",
            "fa-internet-explorer",
            "fa-intercom",
            "fa-instalod",
            "fa-instagram-square",
            "fa-instagram",
            "fa-innosoft",
            "fa-imdb",
            "fa-ideal",
            "fa-hubspot",
            "fa-html5",
            "fa-houzz",
            "fa-hotjar",
            "fa-hornbill",
            "fa-hooli",
            "fa-hive",
            "fa-hire-a-helper",
            "fa-hips",
            "fa-hackerrank",
            "fa-hacker-news-square",
            "fa-hacker-news",
            "fa-gulp",
            "fa-guilded",
            "fa-grunt",
            "fa-gripfire",
            "fa-grav",
            "fa-gratipay",
            "fa-google-wallet",
            "fa-google-plus-square",
            "fa-google-plus-g",
            "fa-google-plus",
            "fa-google-play",
            "fa-google-pay",
            "fa-google-drive",
            "fa-google",
            "fa-goodreads-g",
            "fa-goodreads",
            "fa-gofore",
            "fa-glide-g",
            "fa-glide",
            "fa-gitter",
            "fa-gitlab",
            "fa-gitkraken",
            "fa-github-square",
            "fa-github-alt",
            "fa-github",
            "fa-git-square",
            "fa-git-alt",
            "fa-git",
            "fa-gg-circle",
            "fa-gg",
            "fa-get-pocket",
            "fa-galactic-senate",
            "fa-galactic-republic",
            "fa-fulcrum",
            "fa-freebsd",
            "fa-free-code-camp",
            "fa-foursquare",
            "fa-forumbee",
            "fa-fort-awesome-alt",
            "fa-fort-awesome",
            "fa-fonticons-fi",
            "fa-fonticons",
            "fa-font-awesome-flag",
            "fa-font-awesome-alt",
            "fa-font-awesome",
            "fa-fly",
            "fa-flipboard",
            "fa-flickr",
            "fa-firstdraft",
            "fa-first-order-alt",
            "fa-first-order",
            "fa-firefox-browser",
            "fa-firefox",
            "fa-figma",
            "fa-fedora",
            "fa-fedex",
            "fa-fantasy-flight-games",
            "fa-facebook-square",
            "fa-facebook-messenger",
            "fa-facebook-f",
            "fa-facebook",
            "fa-expeditedssl",
            "fa-evernote",
            "fa-etsy",
            "fa-ethereum",
            "fa-erlang",
            "fa-envira",
            "fa-empire",
            "fa-ember",
            "fa-ello",
            "fa-elementor",
            "fa-edge-legacy",
            "fa-edge",
            "fa-ebay",
            "fa-earlybirds",
            "fa-dyalog",
            "fa-drupal",
            "fa-dropbox",
            "fa-dribbble-square",
            "fa-dribbble",
            "fa-draft2digital",
            "fa-docker",
            "fa-dochub",
            "fa-discourse",
            "fa-discord",
            "fa-digital-ocean",
            "fa-digg",
            "fa-diaspora",
            "fa-dhl",
            "fa-deviantart",
            "fa-dev",
            "fa-deskpro",
            "fa-deploydog",
            "fa-delicious",
            "fa-deezer",
            "fa-dashcube",
            "fa-dailymotion",
            "fa-cpanel",
            "fa-cotton-bureau",
            "fa-contao",
            "fa-connectdevelop",
            "fa-confluence",
            "fa-codiepie",
            "fa-codepen",
            "fa-cloudversify",
            "fa-cloudsmith",
            "fa-cloudscale",
            "fa-cloudflare",
            "fa-chromecast",
            "fa-chrome",
            "fa-centos",
            "fa-centercode",
            "fa-cc-visa",
            "fa-cc-stripe",
            "fa-cc-paypal",
            "fa-cc-mastercard",
            "fa-cc-jcb",
            "fa-cc-discover",
            "fa-cc-diners-club",
            "fa-cc-apple-pay",
            "fa-cc-amex",
            "fa-cc-amazon-pay",
            "fa-canadian-maple-leaf",
            "fa-buysellads",
            "fa-buy-n-large",
            "fa-buromobelexperte",
            "fa-buffer",
            "fa-btc",
            "fa-bootstrap",
            "fa-bluetooth-b",
            "fa-bluetooth",
            "fa-blogger-b",
            "fa-blogger",
            "fa-blackberry",
            "fa-black-tie",
            "fa-bity",
            "fa-bitcoin",
            "fa-bitbucket",
            "fa-bimobject",
            "fa-behance-square",
            "fa-behance",
            "fa-battle-net",
            "fa-bandcamp",
            "fa-aws",
            "fa-aviato",
            "fa-avianex",
            "fa-autoprefixer",
            "fa-audible",
            "fa-atlassian",
            "fa-asymmetrik",
            "fa-artstation",
            "fa-apple-pay",
            "fa-apple",
            "fa-apper",
            "fa-app-store-ios",
            "fa-app-store",
            "fa-angular",
            "fa-angrycreative",
            "fa-angellist",
            "fa-android",
            "fa-amilia",
            "fa-amazon-pay",
            "fa-amazon",
            "fa-alipay",
            "fa-algolia",
            "fa-airbnb",
            "fa-affiliatetheme",
            "fa-adversal",
            "fa-adn",
            "fa-acquisitions-incorporated",
            "fa-accusoft",
            "fa-accessible-icon",
            "fa-500px",
            "fa-creative-commons",
            "fa-creative-commons-by",
            "fa-creative-commons-nc",
            "fa-creative-commons-nc-eu",
            "fa-creative-commons-nc-jp",
            "fa-creative-commons-nd",
            "fa-creative-commons-pd",
            "fa-creative-commons-pd-alt",
            "fa-creative-commons-sa",
            "fa-creative-commons-sampling",
            "fa-creative-commons-sampling-plus",
            "fa-creative-commons-share",
            "fa-creative-commons-zero",
            "fa-creative-commons-remix",
            "fa-css3",
            "fa-css3-alt",
            "fa-cuttlefish",
            "fa-adobe",
            "fa-d-and-d",
            "fa-d-and-d-beyond",
            "fa-tripadvisor"
        ];

        return \in_array($cssClass, $fontAwesome5brands, true);
    }    
}
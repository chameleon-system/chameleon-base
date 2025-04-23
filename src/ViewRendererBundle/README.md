Chameleon System ViewRendererBundle
===================================

Chameleon 4 View Renderer
======================================

In Chameleon 4 löst ein neues System zum Erstellen von Frontendmodulen
das bestehende ab. Damit einhergehend wird auch die Art, wie Templates
erstellt und verwaltet werden, überarbeitet.

Das neue System verwendet von Haus aus Twig[^1] als Templateengine. Die
Architektur erlaubt es aber, die verfügbaren Templatesysteme in Zukunft
durch weitere zu ergänzen.

Twig
====

Twig ist ein Templatesystem, das von Fabien Potencier[^2] entwickelt
wurde, dem Autor von Symfony[^3].

Twig verfügt über eine DSL (Domain specific language), die es erlaubt, reine Viewlogik im
Template zu implementieren. Die wichtigsten sind loops und conditionals.

Eine direkte Verarbeitung von Objekten (z.B. TShopArticle) geschieht
nicht mehr im Template. Das Template erwartet bereits vorbereitete Daten
(z.B. Titel, Preis, Beschreibung). Woher es diese Daten bekommt, ist für
das Template vollkommen transparent. Hierfür sind Mapper zuständig.

Einfügen von Werten
-------------------

Werte im Twigtemplate werden mit zwei curly brackets eingefasst und im
Zuge des Renderingprozesses ersetzt:

    <h1>Hello, {{ name }}.</h1>

Filter
------

Eingefügte Werte können durch Filter geschickt werden. Diese werden mit
einem Pipecharakter (`|`) hinter die Variable geschrieben. Filter können
auch in Reihe geschaltet werden.

Eine umfassende Liste ist unter
http://twig.sensiolabs.org/doc/filters/index.html zu finden.

Einige nützliche Filter sind:

### raw

Standardmäßig werden alle Werte vor der Ausgabe escapet. Das kann durch
den `raw` Filter verhindert werden - beispielsweise, wenn der Wert (gewünschte) HTML-Entities enthält.

    <div>{{dropdown_element|raw}}</div>

### default

Der `default` Filter lässt den Designer einen standard Wert angeben, sollte die gewünschte Variable nicht gesetzt sein.

    {{greeting|default('Why hello there') }} good sir.

### trans

Der `trans` Filter wird von Chameleon implemetiert, sodass intern die
TGlobal::Translate Funktion zur Übersetzung verwendet wird

    {{message|trans}}

`trans` kann - und wird in den meisten Fällen - als Tag verwendet
werden. Somit wird es wesentlich einfacher, ganze Textblöcke zu
übersetzen.

    {% trans %}Why hello good sir.{% endtrans %}

Das `trans` Tag erlaubt auch die Nutzung von Platzhaltern für
eingesetzte Werte:

    {% trans with {'name':name} %}
    Why hello good sir. People call me [{name}].
    {% endtrans %}

Dummy Daten
-----------

Es ist möglich - und üblich - einem Snippet Dummy Daten mitzugeben.
Diese können vom Sinppet Galerie Modul verwendet werden, um das Snippet
autark ohne Mapper mit Daten zu befüllen.

Um Dummy Daten anzulegen genügt es, ein File im selben Ordner des
snippets abzulegen. Die Namenskonvention ist hierbei
`<snippetname>.dummy.php`

Innerhalb des Dummyfiles wird ein Array mit den gewünschten Daten
angelegt und per `return` zurückgegeben.

    $foo = array(
      'title' => 'dummytitle';
    );

    return $foo;

CSS/JS/LESS Einbindung
----------------------

Snippetpackages können ihre eigenen Stylesheets (im CSS und im LESS
Format) und Javascriptdateien liefern (vergleichbar mit den bisherigen
`HTMLHeadIncludes`). Sie werden über ein yaml File im selben Ordner wie
die snippets definiert.

Dieses File hat immer den Namen "config.yml".

Ein config file überschreibt ein anderes (zum Beispiel aus dem core)
immer komplett, wenn es definiert ist. Das heißt, es reicht nicht, nur
ergänzende Resourcen zu definieren. Der Grund hierfür ist, dass es
andernfalls nicht möglich wäre, beispielsweise andere Versionen der
selben Library zu verwenden, da bei einer Komulierung beide Versionen
geladen werden würden.

Ein config file hat potentiell vier Sektionen, wobei alle optional sind
und nicht vorhanden sein müssen:

    less:
       - /assets/snippets/shopFilter/shopFilterItem.less
       - /assets/snippets/shopFilter/shopFilter.less
    css:
       - /assets/snippets/shopFilter/shopFilterItem.css
    js:
       - /assets/snippets/shopFilter/shopFilterItem.js
    include:
       - pkgArticleList
       - common/list

`less`, `css` und `js` geben selbsterklärend die zu ladenden Resourcen
des jeweiligen Typs an. Der Pfad geht vom Webroot aus.

`include` läd zudem alle in den angegebenen Packages konfigurierten
Resourcen. Das ist wichtig, wenn es snippets in dem Package gibt, welche
andere snippets aus anderen packages includen.

### Einbinden von Twigresourcen in alten Modulen

Bei neuen Modulen kümmert sich das Renderingsystem selbständig um die
korrekte Einbindung der konfigurierten Resourcen.

In alten Modulen muss man sich wie bisher selbst darum kümmern, dass die
korrekten Resourcen geladen werden.

Hierfür steht die Methode `getResourcesForSnippetPackage` in
`TUserModelBaseCore` zur Verfügung, die als Parameter einen
Snippetpackagenamen entgegennimmt und ein Array mit den benötigten
Resourcen zurückliefert (inklusive aller in den config Files definierten
includes). Diese Methode kann in der `GetHtmlHeadIncludes` Methode des
jeweiligen Moduls verwendet werden:

          public function GetHtmlHeadIncludes()
          {
              $aIncludes = parent::GetHtmlHeadIncludes();
              $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('userInput/form'));
              $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('textBlock'));

              return $aIncludes;
          }

Sollte also ein Modul in seinem old-style view.php File einen
ViewRenderer nutzen, muss auf diesem Wege sichergestellt werden, dass
auch die richtigen Resourcen nachgeladen werden.

Der ViewRender kann sich an dieser Stelle nicht darum kümmern, da er bei
eingeschaltetem Caching unter Umständen gar nicht mehr instanziert wird.

includes
--------

Snippets können andere Snippets einfügen. Hierfür steht das
`include`-Tag[^5] zur Verfügung.

    Why hello good sir.<br />
    {% include 'pkgGreeting/introduction.html.twig' %}

Das eingebundene Snippet verfügt dann über die selben Input-Werte wie
das Snippet, welches es einsetzt. Zusätzlich können mit dem `with`
Keyword (vgl. `trans` Filter) zusätzliche Werte mitgegeben werden.

Vererbung
---------

Snippets verfügen über ein System, welches es erlaubt, einzelene
Snippets von anderen erben zu lassen.

Als Beispiel seien folgende drei Snippets definiert:
`baseteaser.html.twig`

    <div class="teaser">
    <img src="{{teaserimage}}" />
    {% block teasercontent %}
    ---here is the content---
    {% endblock %}
    </div>

`articleteaser.html.twig`

    {% extends "baseteaser.html.twig" %}

    {% block teasercontent %}
    <span class="intro">
      {{content}}
    </span>
    {% endblock %}

`hugearticleteaser.html.twig`

    {% extends "baseteaser.html.twig" %}

    {% block teasercontent %}
    <h1>title</h1>
    <span class="huge intro">
      {{content}}
    </span>
    {% endblock %}

Es ist nun möglich, einen normalen Teaser mit `articleteaser.html.twig`
zu rendern und einen großen mit `hugearticleteaser.html.twig`. Beide
werden ihren content auf ihre Weise rendern und den `teasercontent`
block in `baseteaser.html.twig` ersetzen.

Speicherorte
------------

Die Snippets werden in einem von drei Ordnern abgelegt. Diese
funktionieren wie die `.../library/classes`-Ordner, das heißt, ein
Snippet in Custom-Core überschreibt automatisch ein Snippet in Core und
ein Snippet im Customer-Bereich überschreibt alle bisherigen.

Wenn ein Snippet überschrieben wird, müssen auch die Dummy-Daten neu
angelegt werden. Es ist beispielsweise nicht möglich, Core-Dummy-Daten
mit einem Snippet im Custom-Core zu verwenden.

Die Ordnerstruktur sollte sinnvollerweise dem Einsatzgebiet entsprechen.
Das heißt, für einfache, allgemein verwendbare Snippets kann es Ordner
geben wie `lists`, `links`, `boxes` etc. Für speziefische Snippets
können Unterordner mit den Namen der Pakete, zu denen sie gehören,
angelegt werden.

Snippet Galerie
---------------

Chameleon beinhaltet im Core ein Snippet Galerie Modul, welches alle
vorhandenen Snippets auflistet und rendert. Es ist möglich das Modul auf
einer Frontenseite einzubinden und somit alle verfügbaren Snipptes zu
sehen und zu testen.

Das Modul verwendet zum Rendern die Dummy-Daten, sollten sie vorhanden
sein.

Die Mapper
==========

Ein Mapper tranformiert Daten aus Objekten in eine Form, wie sie ein
Snippet erwartet. Jeder Mapper erbt von `AbstractViewMapper`.

Ein Snippet bekommt beispielsweise nicht wie bisher ein `TShopArticle`
Objekt und holt sich dort die benötigten Daten heraus. Dafür ist nun ein
Mapper zuständig. Das hat zur Folge, dass ein Snippet nur noch einen
Titel, ein Bild, eine Beschreibung usw. verwendet und es einen oder
mehrere Mapper gibt, die genau diese Daten liefern. Es kann also einen
Mapper geben, der genau diese Daten aus einem `TShopArticle` zieht und
einen zweiten, der die selben Daten aus einem `TShopManufacturer`
extrahiert. Das Snippet bleibt das selbe, und das Modul entscheidet, aus
welchen Objekten das Snippet die Daten tatsächlich bekommt.

Mapper Chains
-------------

Mapper können miteinander verkettet werden, so dass ein Snippet von
mehreren Mappern mit Daten versorgt werden kann.

Wenn ein Snippet beispielsweise nicht nur die Daten eines Produkts
ausgibt, sondern auch noch den Namen des angemeldeten Users, kann das
Modul den Artikel und den User in die Chain geben und die dazugehörigen
Mapper hintereinander die benötigten Daten extrahieren lassen:

    Modul -> ArticleMapper -> UserMapper -> Snippet

Implementierung
---------------

Ein Mapper erweitert immer die Klasse `AbstractViewMapper` und muss
mindestens die Methoden `GetRequirements` und `Accept` implementieren.

### GetRequirements

Hier gibt der Mapper an, welche source objects er erwartet. D.h. ein
Mapper, der sich aus `TShopArticle` bedient, muss hier angeben, dass er
dies vor hat.

    $oRequirements->NeedsSourceObject("oShopArticle", "TShopArticle", null);

Es ist möglich, einen Typ (hier `TShopArticle`) und/oder einen default
Wert (hier `null`) mit anzugeben. Diese Parameter sind optional.

### Accept

In der Accept Methode findet das eigentliche Mapping statt. Der hier zur
Verfügung stehenden `Visitor` stellt alle Objekte zur Verfügung, die der
Mapper in `GetRequirements` angefordert hat.

Auf dem `Visitor` setzt er dann auch alle seine Mappings mit der
`SetMappedValue` Methode:

    $oArticle = $oVisitor->GetSourceObject("oShopArticle");
    $oVisitor->SetMappedValue("title", $oArticle->GetName());

Caching
-------

In jedem Mapper müssen, sofern man von Caching gebrauch machen möchte,
die Cachetrigger gesetzt werden. Hierfür bekommt der Mapper einen
`IMapperCacheTriggerRestricted` in die `Accept` Methode gereicht. Dieser
nimmt die Trigger entgegen. Das erste Argument erhält den `table name`,
das optionale zweite die `id`.

    $oCacheTriggerManager->addTrigger("shop", $sShopId);
    $oCacheTriggerManager->addTrigger("shop_article", $sArticleId);
    $oCacheTriggerManager->addTrigger("data_extranet_user");

Es ist möglich, diesen Vorgang von dem Wert in `bCachingEnabled`
abhängig zu machen, um nicht unnötige Ressourcen durch eventuell nicht
verwendete Trigger zu verbrauchen.

Der ViewRenderer
================

Der `ViewRenderer` verbindet Mapper mit Views und bildet die
Managementschnittstelle zwischen den beiden. Er ist das Interface, das
es dem User ermöglicht, das zugrundeliegende System zu verwenden.

Auch das neue Modulsystem verwendet den `ViewRender` intern und dadurch
kommt der User im Falle eines normalen Moduls nicht direkt in Berührung
damit.

Separate Verwendung
-------------------

Es ist es möglich - und in bestimmten Fällen auch notwendig - selbst
einen View mit Hilfe des `ViewRenderer` zu konfigurieren und zu rendern.

    $oViewRenderer = new ViewRenderer();
    $oViewRenderer->AddSourceObject("oShopArticle", $oShopArticle);
    $oViewRenderer->AddSourceObject("oShopManufacturer", $oShopManufacturer);
    $oViewRenderer->AddMapper(new ArticleToContentMapper());
    $oViewRenderer->AddMapper(new ManufacturerToNameMapper());
    $renderedHTML = $oViewRenderer->Render("article/article_detail.html.twig");

Neue Module
===========

Ein Modul ist ein spezieller Mapper, der
`MTPkgViewRendererAbstractModuleMapper` erweitert und alle benötigten
Objekte in die (optionale) Mapperchain gibt. Dabei verhält er sich wie
ein gewöhnlicher Mapper, mit dem Unterscheid, dass er keine source
objects aus dem Visitor bekommt, sondern sie selbst erstellt.

Die `Accept`-Methode ist hierbei mit der `Execute`-Methode aus den alten
Modulen vergleichbar. Die gewohnten Methoden (wie beispielsweise die
`Init`-Methode) stehen auch hier zur Verfügung, das
`MTPkgViewRendererAbstractModuleMapper` eine Erweiterung von
`TUserCustomModelBase` darstellt.

Es ist möglich, und in vielen einfachen Modulen auch üblich, dass das
Modul direkt in einen View mappt, ohne den Umweg über weitere Mapper zu
nehmen. Es ist aber wichtig, dieses Konzept zu verstehen und zu
verinnerlichen, da es den Weg zu einem sehr flexiblen und erweiterbaren
System öffnet.

Schritt für Schritt zum neuen Modul
-----------------------------------

Es soll ein Modul angelegt werden, das den Namen des aktuell
angemeldeten Benutzers und die verlinkten Namen der Produkte auf seinem
Merkzettel auflistet.

Das Snippet anlegen
-------------------

Wir beginnen damit, das HTML in twig-form für das Frontend zu
definieren. Hieraus wird sich dann ergeben, welche Daten wir tatsächlich
für die Anzeige benötigen werden.

Das erstellte Snippet können wir mit dummy Daten füllen und bereits in
der Snippet Galerie rendern und validieren.

### Das Snippet

Thematisch wird das Modul im Context des `DataExtranetUser` verwendet
werden, daher werden wir unter `snippets/pkgExtranet/DataExtranetUser/`
im `extensions` Bereich ein neues Snippet anlegen.
`noticelist.html.twig`

    {#
      - username
      - articles: array of array("link"=>"", "title"=>""
    #}
    <div class="wishlist">
        <span class="title">
          {% trans with {"username":username} %}
            Der Merkzettel von [{username}]:
          {% endtrans %}
        </span>
        <ul>
          {% for article in articles %}
            <li><a href="{{article.link}}">{{article.title}}</a></li>
          {% endfor %}
        </ul>
    </div>

Im Kommentar legen wir fest, welche parameter das Snippet erwartet. Das
ist eine reine Konvention, die es erleichtern soll, demjenigen, der das
Snippet verwendet, die entsprechenden Mapper zu wählen bzw. zu
implementieren.

### Die Dummydaten

Neben dem Snippet legen wir die Datei mit den Dummydaten an.
`noticelist.dummy.php`

    <?php
    $dummyData = array(
        "username" => "Dummy User",
        "articles" => array(
            array(
                "link" => "#",
                "title" => "Dummy Article 1"
            ),
            array(
                "link" => "#",
                "title" => "Dummy Article 2"
            )
        )
    );

    return $dummyData;

Den/die Mapper wählen/anlegen
-----------------------------

> $Hinweis$: Um das Konzept der Mapper deutlich zu machen, werden wir
> für die anstehende Aufgabe einen separaten Mapper anlegen und ihn in
> der Chain nutzen. Man kann die beschriebene Funktionalität auch direkt
> in die Modulklasse bauen, da diese ja selbst nichts anderes als der
> erste Mapper in der Chain ist.

Der Mapper hat die Aufgabe, aus einem `TDataExtranetUser` Objekt sowohl
den Namen zu mappen, als auch die NoticeList Items und diese in die
gewünschte Form bringen.

Wir können hierfür beliebig bereits vorhandene Mapper kombinieren und
durch eigene ergänzen.

Da es zum Zeitpunkt dieses Tutorials noch keinen bestehenden Mapper für
die `TDataExtranetUser` Objekte gibt, werden wir einen speziefischen
anlegen, der genau diese Arbeit für uns erledigt.

> $Hinweis$: in diesem Fall hängt unser Snippet nur von Daten ab, die
> alle aus dem `TDataExtranetUser` kommen. Es ist aber durchaus möglich,
> dass es mehrere unterschiedliche Objekte gibt, die zur Darstellung
> beitragen. Hierbei ist es sinnvoll, die entsprechenden Mapper zu
> verketten.

Da wir für den vorliegenden Fall einen eigenen Mapper schreiben, wollen
wir ihn in unseren Extensions anlegen. Hierfür legen wir eine neue
Klasse unter `pkgExtranet/mapper/` im `extensions` Bereich an.
`TDataExtranetUsertoNameandNoticeListMapper.class.php`

    <?php

    class TDataExtranetUser_to_Name_and_NoticeList_Mapper extends AbstractViewMapper
    {

        public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
        {
            $oRequirements->NeedsSourceObject("oExtranetUser", "TdbDataExtranetUser");
        }

        public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
        {
            /** @var $oExtranetUser TdbDataExtranetUser */
            $oExtranetUser = $oVisitor->GetSourceObject("oExtranetUser");

            $oVisitor->SetMappedValue("username", $oExtranetUser->fieldName);

            /** @var $oLists TdbShopUserNoticeList */
            $oList = $oExtranetUser->GetFieldShopUserNoticeListList();

            $aArticles = array();

            /** @var $oItem TdbShopUserNoticeList */
            $oItem = null;
            while($oItem = $oList->Next()){
                $oArticle = TShopArticle::GetNewInstance($oItem->fieldShopArticleId);
                $aArticles[] = array(
                    "title" => $oArticle->GetName(),
                    "link" => $oArticle->GetDetailLink()
                );
            }

            $oVisitor->SetMappedValue("articles", $aArticles);
        }
    }

Das Modul anlegen
-----------------

Nun fehlt nur noch das Modul, welches den Mapper mit den gewünschten
Daten füllt.

### Das Modul im Code

Das Modul legen wir unter `pkgExtranet/objects/WebModules` im
`extensions` Bereich an und lassen es
`MTPkgViewRendererAbstractModuleMapper` erweitern.
`PkgExtranetNoticeListModule.class.php`

    <?php

    class PkgExtranetNoticeListModule extends MTPkgViewRendererAbstractModuleMapper
    {
        public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
        {
            $oActiveUser = TDataExtranetUser::GetInstance();
            $oVisitor->SetMappedValue("oExtranetUser", $oActiveUser);
        }
    }

Das Modul gibt wie erwartet das aktuelle User Objekt dem
`IMapperVisitorRestricted` in ein mapped value.

### Das Modul im Backend

Im Backend können wir nun das Modul wie gewohnt unter `Template Module`
anlegen. Als Klasse geben wir unsere angelegte
`PkgExtranetNoticeListModule` Klasse an. In der Konfiguration müssen wir
nun mindestens einen View anlegen, welcher sich aus dem angelegten
Snippet und dem dazugehörigen Mapper zusammensetzt. Das geschieht im
Feld `View/Mapper Konfiguration`:

    standard=pkgExtranet/DataExtranetUser/noticelist.html.twig;TDataExtranetUser_to_Name_and_NoticeList_Mapper

Somit steht nun die Konfiguration unter dem Namen `standard` im
`ModuleChooser` zur Verfügung.

Grafischer Überblick über das neue System
=========================================

![image](doc/mapper.png)



[^1]: http://twig.sensiolabs.org/

[^2]: http://fabien.potencier.org/

[^3]: http://symfony.com/

[^5]: http://twig.sensiolabs.org/doc/tags/include.html

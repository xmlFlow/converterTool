<?php namespace docx2tei\tei;


use docx2tei\structure\Document;
use DOMDocument;
use DOMElement;
use DOMXPath;

class TEIDocument extends DOMDocument {
    var $cfg;
    var $root;
    var $teiHeader;
    var $text;
    var $body;
    var $structuredDocument;
    var $xpath;
    var $headers = array();



    public function __construct(Document $structuredDocument, $config) {
        $this->structuredDocument = $structuredDocument;
        $this->xpath = new DOMXPath($structuredDocument);
        $this->cfg = $config;
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->setStructure();
        $this->isCorrectStructure();
        $headers = new Headers($this);

        //TODO  first replace all the small entries, then SB
        $facsimiles = new Facsimiles($this);
        $abstract = new Abstracts($this);
        $edition = new Edition($this);



        $englishTranslation = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->et . '"]/parent::sec');
        $synopsis = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->synopsis . '"]/parent::sec');
        $translation = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->translation . '"]/parent::sec');
        $commentary = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->commentary . '"]/parent::sec');
        $tokens = explode('#', 'त#SB्तमकर्ण्णधारः<p> श्रीलोकनाथचरणं #pln{place_with_unique_id}#भवतो भजेहं ।। ।। </p>श्#SEरेयोऽस्त');


    }
    function renameElement($element, $newName) {
        $newElement = $element->ownerDocument->createElement($newName);
        $parentElement = $element->parentNode;
        $parentElement->insertBefore($newElement, $element);

        $childNodes = $element->childNodes;
        while ($childNodes->length > 0) {
            $newElement->appendChild($childNodes->item(0));
        }

        $attributes = $element->attributes;
        while ($attributes->length > 0) {
            $attribute = $attributes->item(0);
            if (!is_null($attribute->namespaceURI)) {
                $newElement->setAttributeNS('http://www.w3.org/2000/xmlns/',
                    'xmlns:'.$attribute->prefix,
                    $attribute->namespaceURI);
            }
            $newElement->setAttributeNode($attribute);
        }

        $parentElement->removeChild($element);
        return $newElement;
    }


    function isCorrectStructure(): bool {
        $correct = true;
        $correct = $this->isCorrectSections();
        $correct = $this->isCorrectHeaders();

        return $correct;

    }

    /**
     * @return bool
     */
    function isCorrectSections(): bool {
        $sectionNodes = $this->xpath->query("//root/text/sec/title");
        foreach ($sectionNodes as $section) {
            if (!in_array($section->nodeValue, (array)$this->cfg->sections)) {
                // specially handle Editions
                if (!preg_match("/Edition(\s)*\((.)*\)/i", $section->nodeValue)) {
                    $this->print_error("[Error] Section missing or wrong : " . $section->nodeValue);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $value
     */
    function print_error($message): void {
        echo("" . $message . "\n");
        //error_log($message."\n");
    }

    function isCorrectHeaders(): bool {
        return true;
    }



    function setStructure() {

        $this->root = $this->createElement('TEI');
        $this->root->setAttributeNS(
            "http://www.w3.org/2000/xmlns/",
            "xmlns",
            "http://www.tei-c.org/ns/1.0"
        );
        $this->root->setAttribute('xml:id', $this->headers["h4"] ?? "");
        $this->appendChild($this->root);

        $this->teiHeader = $this->createElement('teiHeader');
        $this->root->appendChild($this->teiHeader);

        $this->text = $this->createElement('text');
        $this->body = $this->createElement('body');
        $this->text->appendChild($this->body);
        $this->root->appendChild($this->text);

    }



    public function saveToFile(string $pathToFile) {
        $this->save($pathToFile);
    }



}
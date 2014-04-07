<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 25/3/2014
 * Time: 1:35 πμ
 */

namespace Application\Model;


class SitemapXmlParser
{
    /**
     * @var \XmlWriter
     */
    private $writer;

    private $openElements;

    public function __construct()
    {
        $this->writer = new \XMLWriter('UTF-8');
    }

    public function begin()
    {
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->setIndentString(str_repeat(' ', 4));

        $this->writer->startDocument('1.0', 'UTF-8');
    }

    public function addHeader($type, $hasFeeds = false)
    {
        $this->writer->startElement($type);
        $this->writer->writeAttribute('xmlns', "http://www.sitemaps.org/schemas/sitemap/0.9");
        if ($hasFeeds) {
            $this->writer->writeAttribute('xmlns:image', "http://www.google.com/schemas/sitemap-image/1.1");
            $this->writer->writeAttribute('xmlns:video', "http://www.google.com/schemas/sitemap-video/1.1");
        }
        $this->writer->endAttribute();
        $this->openElements++;
        return $this;
    }

    public function close()
    {
        for ($i = 0; $i < $this->openElements; $i++) {
            $this->writer->endElement();
        }
        $this->openElements = 0;
        $this->writer->endDocument();
    }

    public function addSitemap($value)
    {
        $this->writer->startElement('sitemap');
        $this->writer->writeElement('loc', $value);
        $this->writer->writeElement('lastmod', date(\Datetime::ATOM, time()));
        $this->writer->endElement();
    }

    public function addUrl($value)
    {
        $this->writer->startElement('url');
        $this->writer->writeElement('loc', $value);
        $this->writer->endElement();
    }

    /**
     * @param \Feed\Entity\Feed $feed
     */
    public function addFeedInfo($feed)
    {
        $ogTags = $feed->getOgTags();
        $this->writer->startElement('url');
        $this->writer->writeElement('loc', "http://www.leetfeed.com/feed/" . $feed->getFeedId());
        $this->writer->writeElement('lastmod', date(\Datetime::ATOM, time()));
        $this->writer->startElement('image:image');
        $this->writer->writeElement('image:loc', $feed->getThumbnail());
        $this->writer->endElement();
        $this->writer->startElement('video:video');
        $this->writer->writeElement('video:player_loc', $ogTags["video"]);
        $this->writer->writeElement('video:thumbnail_loc', $ogTags["image"]);
        $this->writer->writeElement('video:title', 'League of Legends - ' . $ogTags["title"]);
        $this->writer->writeElement('video:description', $ogTags["description"]);
        $this->writer->endElement();
        $this->writer->endElement();
    }

    public function flush()
    {
        $this->writer->flush();
    }

    public function show()
    {
        echo $this->writer->outputMemory();
    }
} 
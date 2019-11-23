<?php

namespace EditorialBundle\Tests\Util;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Util\FileNameUtil;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileNameUtilTest extends TestCase
{
    /**
     * @dataProvider provideGetArticleVersionFileName
     */
    public function testGetArticleVersionFileName($articleId, $versionId, $expectedName)
    {
        /** @var Article|MockObject $article */
        $article = $this->createArticleMock();
        /** @var ArticleVersion|MockObject $version */
        $version = $this->createArticleVersionMock();

        $version->method('getArticle')->willReturn($article);
        $version->method('getId')->willReturn($versionId);
        $article->method('getId')->willReturn($articleId);

        $this->assertSame($expectedName, FileNameUtil::getArticleVersionFileName($version));
    }

    public function provideGetArticleVersionFileName()
    {
        return [
            [0, 0, '0-0'],
            [0, 1, '0-1'],
            [1, 0, '1-0'],
            [1, 1, '1-1'],
            [2, 2, '2-2'],
        ];
    }

    /**
     * @dataProvider provideGetArticleVersionDisplayFileName
     */
    public function testGetArticleVersionDisplayFileName($name, $created, $suffix, $expectedName)
    {
        /** @var Article|MockObject $article */
        $article = $this->createArticleMock();
        /** @var ArticleVersion|MockObject $version */
        $version = $this->createArticleVersionMock();

        $version->method('getArticle')->willReturn($article);
        $version->method('getSuffix')->willReturn($suffix);
        $version->method('getCreated')->willReturn($created);
        $article->method('getName')->willReturn($name);

        $this->assertSame($expectedName, FileNameUtil::getArticleVersionDisplayFileName($version));
    }

    public function provideGetArticleVersionDisplayFileName()
    {
        return [
            ['', null, '', '-00000000.'],
            ['foo', new \DateTime("2000-01-01"), 'docx', 'foo-20000101.docx'],
            ['foo1', new \DateTime("2011-02-03"), 'pdf', 'foo1-20110203.pdf'],
            ['foo2', new \DateTime("2100-10-11"), 'html', 'foo2-21001011.html'],
        ];
    }

    /**
     * @dataProvider provideGetMagazineFileName
     */
    public function testGetMagazineFileName($id, $expectedName)
    {
        /** @var Magazine|MockObject $magazine */
        $magazine = $this->createMagazineMock();
        $magazine->method('getId')->willReturn($id);

        $this->assertSame($expectedName, FileNameUtil::getMagazineFileName($magazine));
    }

    public function provideGetMagazineFileName()
    {
        return [
            [0, '0'],
            [1, '1'],
            [2, '2'],
            [3, '3'],
        ];
    }

    /**
     * @dataProvider provideGetMagazineDisplayFileName
     */
    public function testGetMagazineDisplayFileName($year, $number, $suffix, $expectedName)
    {
        /** @var Magazine|MockObject $magazine */
        $magazine = $this->createMagazineMock();
        $magazine->method('getYear')->willReturn($year);
        $magazine->method('getNumber')->willReturn($number);
        $magazine->method('getSuffix')->willReturn($suffix);

        $this->assertSame($expectedName, FileNameUtil::getMagazineDisplayFileName($magazine));
    }

    public function provideGetMagazineDisplayFileName()
    {
        return [
            [0, 0, 'pdf', 'rocnik0-cislo0.pdf'],
            [0, 1, 'pdf', 'rocnik0-cislo1.pdf'],
            [1, 0, 'pdf', 'rocnik1-cislo0.pdf'],
            [1, 1, 'pdf', 'rocnik1-cislo1.pdf'],
            [2, 3, 'html', 'rocnik2-cislo3.html'],
            [10, 11, 'docx', 'rocnik10-cislo11.docx'],
        ];
    }

    //private

    private function createArticleMock()
    {
        return $this->getMockBuilder(Article::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function createArticleVersionMock()
    {
        return $this->getMockBuilder(ArticleVersion::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }

    private function createMagazineMock()
    {
        return $this->getMockBuilder(Magazine::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}

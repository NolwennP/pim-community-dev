<?php

namespace tests\integration\Pim\Bundle\ApiBundle\Controller\Rest\Category;

use Symfony\Component\HttpFoundation\Response;
use Test\Integration\TestCase;

class CreateCategoryIntegration extends TestCase
{
    protected $purgeDatabaseForEachTest = false;

    public function testHttpHeadersInResponseWhenACategoryIsCreated()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": "new_category"
    }
JSON;

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertArrayHasKey('location', $response->headers->all());
        $this->assertSame('http://localhost/api/rest/v1/categories/new_category', $response->headers->get('location'));
    }

    public function testFormatStandardWhenACategoryIsCreatedButUncompleted()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": "new_category"
    }
JSON;

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $category = $this->get('pim_catalog.repository.category')->findOneByIdentifier('new_category');
        $categoryStandard = [
            'code'   => 'new_category',
            'parent' => null,
            'labels' => [],
        ];
        $normalizer = $this->get('pim_catalog.normalizer.standard.category');
        $this->assertSame($categoryStandard, $normalizer->normalize($category));
    }

    public function testCompleteCategoryCreation()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": "categoryC",
        "parent": "master",
        "labels": {
            "en_US": "Category C",
            "fr_FR": "Catégorie C"
        }
    }
JSON;
        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $category = $this->get('pim_catalog.repository.category')->findOneByIdentifier('categoryC');
        $categoryStandard = [
            'code'   => 'categoryC',
            'parent' => 'master',
            'labels' => [
                'en_US' => 'Category C',
                'fr_FR' => 'Catégorie C',
            ],
        ];
        $normalizer = $this->get('pim_catalog.normalizer.standard.category');
        $this->assertSame($categoryStandard, $normalizer->normalize($category));
    }

    public function testResponseWhenContentIsNotValid()
    {
        $client = static::createClient();
        $data = '';

        $expectedContent = [
            'code'    => 400,
            'message' => 'JSON is not valid.',
        ];

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);
        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertSame($expectedContent, json_decode($response->getContent(), true));
    }

    public function testResponseWhenCategoryCodeAlreadyExists()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": "categoryA"
    }
JSON;

        $expectedContent = [
            'code'    => 422,
            'message' => 'Category "categoryA" already exists.',
        ];

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame($expectedContent, json_decode($response->getContent(), true));
    }


    public function testResponseWhenValidationFailed()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": ""
    }
JSON;

        $expectedContent = [
            'code'    => 422,
            'message' => 'Validation failed.',
            'errors'  => [
                [
                    'field'   => 'code',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame($expectedContent, json_decode($response->getContent(), true));
    }

    public function testResponseWhenAPropertyIsNotExpected()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "code": "sales",
        "extra_property": ""
    }
JSON;

        $expectedContent = [
            'code'    => 422,
            'message' => 'Property "extra_property" does not exist. Check the standard format documentation.',
            '_links'  => [
                'documentation' => [
                    'href' => 'https://docs.akeneo.com/1.6/reference/standard_format/other_entities.html#category',
                ],
            ],
        ];

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame($expectedContent, json_decode($response->getContent(), true));
    }

    public function testResponseWhenLabelsIsNull()
    {
        $client = static::createClient();
        $data =
<<<JSON
    {
        "labels": null
    }
JSON;

        $expectedContent = [
            'code'    => 422,
            'message' => 'Property "labels" expects an array (for update category). Check the standard format documentation.',
            '_links'  => [
                'documentation' => [
                    'href' => 'https://docs.akeneo.com/1.6/reference/standard_format/other_entities.html#category',
                ],
            ],
        ];

        $client->request('POST', 'api/rest/v1/categories/', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame($expectedContent, json_decode($response->getContent(), true));
    }
}

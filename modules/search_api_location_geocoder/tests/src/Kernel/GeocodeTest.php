<?php

namespace Drupal\Tests\search_api_location_geocoder\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\search_api_location_geocoder\Plugin\search_api_location\location_input;

/**
 * Test for the geocode plugin.
 *
 * @group search_api_location
 * @coversDefaultClass \Drupal\search_api_location_geocoder\Plugin\search_api_location\location_input\Geocode
 */
class GeocodeTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'search_api',
    'search_api_location',
    'search_api_location_geocoder',
    'geocoder'
  ];

  /**
   * @var \Drupal\search_api_location\LocationInput\LocationInputInterface
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $configuration = [
      'plugins' => [
        'arcgisonline' => [
          'checked' => TRUE,
          'weight' => '-3',
        ],
        'llama' => [
          'checked' => FALSE,
          'weight' => '-3',
        ],
      ],
    ];
    $this->sut = $this->container
      ->get('plugin.manager.search_api_location.location_input')
      ->createInstance('geocode', $configuration);
  }

  /**
   * Test the parsing of input entered by user in text format.
   *
   * @covers ::getParsedInput
   */
  public function testGetParsedInput() {
    $input['value'] = 'Ghent';
    $parsed = $this->sut->getParsedInput($input);
    list($lat, $lng) = explode(',', $parsed);
    $this->assertEquals(round($lat, 0, PHP_ROUND_HALF_DOWN), 51);
    $this->assertEquals(round($lng, 0, PHP_ROUND_HALF_DOWN), 4);
  }

  /**
   * Tests with invalid input.
   *
   * @covers ::getParsedInput
   */
  public function testWithUnexpectedInput() {
    $input = ['animal' => 'llama'];
    $this->setExpectedException(\InvalidArgumentException::class);
    $this->sut->getParsedInput($input);
  }

}
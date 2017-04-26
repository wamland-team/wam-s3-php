<?php
require_once('.env.php');

use App\Services\S3Service;
use Kahlan\Plugin\Monkey;

/**
 * |-------------------------------------------
 * | Testing class FileOperation
 * |-------------------------------------------
 */
describe('FileOperation', function () {

    given('instance', function () {
        return new S3Service(
            AWS_REGION,
            AWS_VERSION,
            AWS_ACCESS_KEY_ID,
            AWS_SECRET_ACCESS_KEY
        );
    });
    
    /**
     * |-------------------------------------------
     * | Methode : isFile()
     * |-------------------------------------------
     */
    describe('.isFile()', function () {

        it('when not valid', function () {
            expect(function () {
                $this->instance->isFile(null);
            })->toThrow();
        });

        it('file not found', function () {
            expect(function () {
                $this->instance->isFile('resources/assets/fixtures/null.png');
            })->toThrow();
        });

        it('file not readable', function () {
            Monkey::patch('is_readable', function () {
                return false;
            });
            expect(function () {
                $this->instance->isFile('src/resources/assets/fixtures/pic.jpg');
            })->toThrow();
        });

        it('when valid', function () {
            expect(
                $this->instance->isFile('src/resources/assets/fixtures/pic.jpg')
            )->toBeTruthy();
        });

    });

    /**
     * |-------------------------------------------
     * | Methode : mime()
     * |-------------------------------------------
     */
    describe('.mime()', function () {

        it('when not valid', function () {
            expect(function () {
                $this->instance->mime(null);
            })->toThrow();
        });

        it('with missing mime_content_type', function () {
            Monkey::patch('mime_content_type', function () {
                return null;
            });
            expect(function () {
                $this->instance->mime('src/resources/assets/fixtures/pic.jpg');
            })->toThrow();
        });

        it('when valid', function () {
            $str = $this->instance->mime('src/resources/assets/fixtures/pic.jpg');
            expect($str)->toBe('image/jpeg');
        });
    });
});

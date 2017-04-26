<?php
require_once('.env.php');

use App\Services\S3Service;
use Kahlan\Plugin\Stub;

/**
 * |-------------------------------------------
 * | S3Service (AWS Amazon)
 * |-------------------------------------------
 */
describe('S3Service', function () {

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
     * | __construct()
     * |-------------------------------------------
     */
    describe('.__construct()', function () {

        it('with empty region', function () {
            expect(function () {
                new S3Service(null, AWS_VERSION, AWS_VERSION, AWS_ACCESS_KEY_ID);
            })->toThrow();
        });

        it('with empty version', function () {
            expect(function () {
                new S3Service(AWS_REGION, null, AWS_VERSION, AWS_ACCESS_KEY_ID);
            })->toThrow();
        });

        it('with empty access_key', function () {
            expect(function () {
                new S3Service(AWS_REGION, AWS_VERSION, null, AWS_ACCESS_KEY_ID);
            })->toThrow();
        });

        it('with empty secret_key', function () {
            expect(function () {
                new S3Service(AWS_REGION, AWS_VERSION, AWS_ACCESS_KEY_ID, null);
            })->toThrow();
        });

        it('when valid', function () {
            expect(
                $this->instance
            )->toBeTruthy();
        });

        it('when not valid', function () {
            Stub::on(Aws\Sdk::class)->method('__construct',
                function () {
                    Throw new Exception();
                }
            );
            expect(function () {
                $this->instance;
            })->toThrow();
        });

        it('when valid (Aws\Sdk::class -> createS3)', function () {
            Stub::on(Aws\Sdk::class)->method('createS3',
                function () {
                    return true;
                }
            );
            expect(
                $this->instance
            )->toBeTruthy();
        });

        it('when not valid (Aws\Sdk::class -> createS3)', function () {
            Stub::on(Aws\Sdk::class)->method('createS3',
                function () {
                    Throw new Exception();
                }
            );
            expect(function () {
                $this->instance;
            })->toThrow();
        });

    });

    /**
     * |-------------------------------------------
     * | put()
     * |-------------------------------------------
     */
    describe('.put()', function () {

        it('with empty bucket', function () {
            expect(function () {
                $this->instance->put();
            })->toThrow();
        });

        it('with empty file', function () {
            expect(function () {
                $this->instance->key = 'key';
                $this->instance->bucket = 'bucket';
                $this->instance->put();
            })->toThrow();
        });

        it('when file is not valid', function () {
            Stub::on($this->instance)->method('isFile',
                function () {
                    Throw new Exception();
                }
            );

            expect(function () {
                $this->instance->key = 'key';
                $this->instance->bucket = 'bucket';
                $this->instance->file = 'file';
                $this->instance->put();
            })->toThrow();
        });

        it('when mime is not valid', function () {

            Stub::on($this->instance)->method('isFile',
                function () {
                    return true;
                }
            );

            Stub::on($this->instance)->method('mime',
                function () {
                    Throw new Exception();
                }
            );

            expect(function () {
                $this->instance->key = 'key';
                $this->instance->bucket = 'bucket';
                $this->instance->file = 'file';
                $this->instance->put();
            })->toThrow();

        });

        it('when mime is not valid', function () {

            Stub::on($this->instance)->method('isFile',
                function () {
                    return true;
                }
            );

            Stub::on($this->instance)->method('mime',
                function () {
                    return true;
                }
            );

            Stub::on($this->instance->s3Client)->method('putObject',
                function () {
                    Throw new Exception();
                }
            );

            expect(function () {
                $this->instance->bucket = 'bucket';
                $this->instance->file = 'file';
                $this->instance->put();
            })->toThrow();
        });

        it('when valid', function () {

            Stub::on($this->instance)->method('isFile',
                function () {
                    return true;
                }
            );

            Stub::on($this->instance)->method('mime',
                function () {
                    return true;
                }
            );

            Stub::on($this->instance->s3Client)->method('putObject',
                function () {
                    return true;
                }
            );

            $this->instance->key = 'key';
            $this->instance->bucket = 'bucket';
            $this->instance->file = 'file';
            expect($this->instance->put())->toBeTruthy();

        });

    });

    /**
     * |-------------------------------------------
     * | get()
     * |-------------------------------------------
     */
    describe('.get()', function () {

        it('with empty bucket', function () {
            expect(function () {
                $this->instance->get();
            })->toThrow();
        });

        it('with empty key', function () {
            expect(function () {
                $this->instance->bucket = 'bucket';
                $this->instance->get();
            })->toThrow();
        });

        it('when not valid (throw)', function () {

            Stub::on($this->instance->s3Client)->method('getObject',
                function () {
                    Throw new Exception();
                }
            );

            expect(function () {
                $this->instance->bucket = AWS_BUCKET;
                $this->instance->key = 'key';
                $this->instance->get();
            })->toThrow();

        });

        it('when not valid (boolean)', function () {

            Stub::on($this->instance->s3Client)->method('getObject',
                function () {
                    return false;
                }
            );

            expect(function () {
                $this->instance->bucket = AWS_BUCKET;
                $this->instance->key = 'key';
                $this->instance->get();
            })->toThrow();

        });

    });

    /**
     * |-------------------------------------------
     * | delete()
     * |-------------------------------------------
     */
    describe('.delete()', function () {

        it('with empty bucket', function () {
            expect(function () {
                $this->instance->delete();
            })->toThrow();
        });

        it('with empty key', function () {
            expect(function () {
                $this->instance->bucket = 'bucket';
                $this->instance->delete();
            })->toThrow();
        });

        it('when not valid', function () {

            Stub::on($this->instance->s3Client)->method('deleteObject',
                function () {
                    Throw new Exception(999);
                }
            );

            expect(function () {
                $this->instance->bucket = AWS_BUCKET;
                $this->instance->key = 'key';
                $this->instance->delete();
            })->toThrow();
        });

    });

});

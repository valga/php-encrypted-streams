<?php
namespace Jsq\EncryptionStreams;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\CachingStream;

trait AesEncryptionStreamTestTrait
{
    public function cartesianJoinInputIvKeySizeProvider()
    {
        $toReturn = [];
        $plainTexts = $this->unwrapProvider([$this, 'plainTextProvider']);
        $ivs = $this->unwrapProvider([$this, 'ivProvider']);
        $keySizes = $this->unwrapProvider([$this, 'keySizeProvider']);

        for ($i = 0; $i < count($plainTexts); $i++) {
            for ($j = 0; $j < count($ivs); $j++) {
                for ($k = 0; $k < count($keySizes); $k++) {
                    $toReturn []= [
                        $plainTexts[$i],
                        clone $ivs[$j],
                        $keySizes[$k],
                    ];
                }
            }
        }

        return $toReturn;
    }
    public function cartesianJoinIvKeySizeProvider()
    {
        $toReturn = [];
        $ivs = $this->unwrapProvider([$this, 'ivProvider']);
        $keySizes = $this->unwrapProvider([$this, 'keySizeProvider']);

        for ($i = 0; $i < count($ivs); $i++) {
            for ($j = 0; $j < count($keySizes); $j++) {
                $toReturn []= [
                    clone $ivs[$i],
                    $keySizes[$j],
                ];
            }
        }

        return $toReturn;
    }

    public function ivProvider()
    {
        return [
            [new CbcIv(openssl_random_pseudo_bytes(
                openssl_cipher_iv_length('aes-256-cbc')
            ))],
            [new CtrIv(openssl_random_pseudo_bytes(
                openssl_cipher_iv_length('aes-256-ctr')
            ))],
            [new EcbIv()],
        ];
    }

    public function keySizeProvider()
    {
        return [
            [128],
            [192],
            [256],
        ];
    }

    public function plainTextProvider() {
        return [
            [Psr7\stream_for('The rain in Spain falls mainly on the plain.')],
            [Psr7\stream_for('دست‌نوشته‌ها نمی‌سوزند')],
            [Psr7\stream_for('Рукописи не горят')],
            [new CachingStream(new RandomByteStream(1024 * 1024 + 11))]
        ];
    }

    private function unwrapProvider(callable $provider)
    {
        return array_map(function (array $wrapped) {
            return $wrapped[0];
        }, call_user_func($provider));
    }
}
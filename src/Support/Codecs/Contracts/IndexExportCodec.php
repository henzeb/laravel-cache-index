<?php

namespace Henzeb\CacheIndex\Support\Codecs\Contracts;

interface IndexExportCodec
{
    public function encode(array $data): string;

    public function decode(string $data): array;
}

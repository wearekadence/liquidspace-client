<?php

namespace Client\Request;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Delete = 'DELETE';
    case Put = 'PUT';
    case Patch = 'PATCH';
}

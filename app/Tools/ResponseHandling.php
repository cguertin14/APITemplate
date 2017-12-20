<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 09/12/17
 * Time: 5:11 PM
 */

namespace App\Tools;
use Dingo\Api\Http\Response;

abstract class ResponseHandling extends Response
{
    // COMMON RESPONSE ERROR CODES
    const EMPTY_COLLECTION = ['error' => 'Requested collection/paginator of data is empty.'];
    const KEYWORD_LENGTH   = ['error' => 'Keyword length must be at least 3 characters.'];
    const QUERY_ERROR      = ['error' => 'Query returned an error (ex: foreign key constraint violated, unique constraint violated, etc.), please try again.'];

    // COMMON RESPONSE WORKING CODES
    const RESOURCE_DELETED = ['status' => 'Resource successfully deleted!'];
}
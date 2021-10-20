<?php namespace VS\ApplicationBundle\Component\Exception;

class PDOException extends \Exception
{
    protected $code = 500;
}
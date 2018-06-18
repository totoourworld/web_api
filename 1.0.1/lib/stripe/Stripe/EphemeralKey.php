<?php

/**
 * Class EphemeralKey
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property int $expires
 * @property bool $livemode
 * @property string $secret
 * @property array $associated_objects
 *
 * @package Stripe
 */
class Stripe_EphemeralKey extends Stripe_ApiResource
{
    /**
     * This is a special case because the ephemeral key endpoint has an
     *    underscore in it. The parent `className` function strips underscores.
     *
     * @return string The name of the class.
     */
    public static function className()
    {
        return 'ephemeral_key';
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return EphemeralKey The created key.
     */
    public static function create($params = null, $opts = null)
    {
        if (!$opts['stripe_version']) {
        	$msg = "stripe_version must be specified to create an ephemeral key";
      		throw new Stripe_InvalidRequestError($msg, null);
        }
        $class = get_class();
        return self::_scopedCreated($class, $params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return EphemeralKey The deleted key.
     */
    public function delete($params = null, $opts = null)
    {
    	$class = get_class();
        return self::__scopedDelete($class, $params, $opts);
    }
}

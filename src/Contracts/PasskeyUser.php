<?php
namespace PioneerDynamics\LaravelPasskey\Contracts;

interface PasskeyUser
{
    /**
     * Get the username for the user
     * 
     * @return string
     */
    public function getUsername();

    /**
     * Get the user id for the user
     * 
     * @return string
     */
    public function getUserId();

    /**
     * Get the user's display name
     * 
     * @return string
     */
    public function getDisplayName();

    /**
     * Get the user's profile picture
     * 
     * This must be a data url
     * 
     * @return string|null
     */
    public function getUserIcon();
}
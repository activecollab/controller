<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\Controller\ResultEncoder\ResultEncoderInterface;

/**
 * @package ActiveCollab\Controller
 */
interface ControllerInterface
{
    /**
     * Return action result encoder.
     *
     * @return ResultEncoderInterface
     */
    public function getResultEncoder();

    /**
     * Set action result encoder.
     *
     * @param  ResultEncoderInterface $result_encoder
     * @return $this
     */
    public function &setResultEncoder(ResultEncoderInterface $result_encoder);

    /**
     * Return controller name, without namespace.
     *
     * @return string
     */
    public function getControllerName();
}

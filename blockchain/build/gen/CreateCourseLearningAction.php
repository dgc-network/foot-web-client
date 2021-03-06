<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: payload.proto

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>CreateCourseLearningAction</code>
 */
class CreateCourseLearningAction extends \Google\Protobuf\Internal\Message
{
    /**
     *learning_id int NOT NULL AUTO_INCREMENT,
     *course_id int NOT NULL,
     *learning_title varchar(255),
     *learning_link varchar(255),
     *
     * Generated from protobuf field <code>int32 learning_id = 1;</code>
     */
    protected $learning_id = 0;
    /**
     * Generated from protobuf field <code>int32 course_id = 2;</code>
     */
    protected $course_id = 0;
    /**
     * Generated from protobuf field <code>string learning_title = 3;</code>
     */
    protected $learning_title = '';
    /**
     * Generated from protobuf field <code>string learning_link = 4;</code>
     */
    protected $learning_link = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $learning_id
     *          learning_id int NOT NULL AUTO_INCREMENT,
     *          course_id int NOT NULL,
     *          learning_title varchar(255),
     *          learning_link varchar(255),
     *     @type int $course_id
     *     @type string $learning_title
     *     @type string $learning_link
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Payload::initOnce();
        parent::__construct($data);
    }

    /**
     *learning_id int NOT NULL AUTO_INCREMENT,
     *course_id int NOT NULL,
     *learning_title varchar(255),
     *learning_link varchar(255),
     *
     * Generated from protobuf field <code>int32 learning_id = 1;</code>
     * @return int
     */
    public function getLearningId()
    {
        return $this->learning_id;
    }

    /**
     *learning_id int NOT NULL AUTO_INCREMENT,
     *course_id int NOT NULL,
     *learning_title varchar(255),
     *learning_link varchar(255),
     *
     * Generated from protobuf field <code>int32 learning_id = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setLearningId($var)
    {
        GPBUtil::checkInt32($var);
        $this->learning_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 course_id = 2;</code>
     * @return int
     */
    public function getCourseId()
    {
        return $this->course_id;
    }

    /**
     * Generated from protobuf field <code>int32 course_id = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setCourseId($var)
    {
        GPBUtil::checkInt32($var);
        $this->course_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string learning_title = 3;</code>
     * @return string
     */
    public function getLearningTitle()
    {
        return $this->learning_title;
    }

    /**
     * Generated from protobuf field <code>string learning_title = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setLearningTitle($var)
    {
        GPBUtil::checkString($var, True);
        $this->learning_title = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string learning_link = 4;</code>
     * @return string
     */
    public function getLearningLink()
    {
        return $this->learning_link;
    }

    /**
     * Generated from protobuf field <code>string learning_link = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setLearningLink($var)
    {
        GPBUtil::checkString($var, True);
        $this->learning_link = $var;

        return $this;
    }

}


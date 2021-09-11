<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('NonFungibleToken')) {

    class NonFungibleToken {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('course_list', __CLASS__ . '::list_mode');
            add_shortcode('course_edit', __CLASS__ . '::edit_mode');
            add_shortcode('course_view', __CLASS__ . '::view_mode');
            self::create_tables();
        }

        // The total number of tokens of this type in existence
        public $totalSupply;

        // Event that emitted when the NFT contract is initialized
        public function ContractInitialized() {}

        // Event that is emitted when a token is withdrawn, indicating the owner
        // of the collection that it was withdrawn from.
        //
        // If the collection is not in an account's storage, `from` will be `nil`.
        public function Withdraw($id, $from){}

        // Event that emitted when a token is deposited to a collection.
        //
        // It indicates the owner of the collection that it was deposited to.
        public function Deposit($id, $to){}

        // Interface that the NFTs have to conform to
        public function INFT() {
            // The unique ID that each NFT has
            $id;
        }

        // Requirement that all conforming NFT smart contracts have
        // to define a resource called NFT that conforms to INFT
        public function NFT() {
            //pub let id: UInt64
        }

    }
}
?>
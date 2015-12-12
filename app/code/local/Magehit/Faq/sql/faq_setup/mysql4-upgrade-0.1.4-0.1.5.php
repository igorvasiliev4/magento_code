<?php

$installer = $this;

$installer->startSetup();

$installer->run("
		ALTER TABLE {$this->getTable('faq')}  MODIFY faq_order int(11) unsigned NOT NULL;
		ALTER TABLE {$this->getTable('faqitem')}  MODIFY faq_order int(11) unsigned NOT NULL;
");

$installer->endSetup(); 
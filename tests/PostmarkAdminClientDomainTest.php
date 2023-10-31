<?php

namespace Postmark\Tests;

require_once __DIR__ . "/PostmarkClientBaseTest.php";

use Postmark\PostmarkAdminClient as PostmarkAdminClient;

class PostmarkAdminClientDomainTest extends PostmarkClientBaseTest {

	static function tearDownAfterClass(): void {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);

		$domains = $client->listDomains();

		foreach ($domains->domains as $key => $value) {
			if (preg_match('/test-php.+/', $value->name) > 0) {
				$client->deleteDomain($value->ID);
			}
		}
	}

	function testClientCanGetDomainList() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);

		$domains = $client->listDomains();

		$this->assertGreaterThan(0, $domains->totalCount);
		$this->assertNotEmpty($domains->domains);
	}

	function testClientCanGetSingleDomain() {
		$tk = parent::$testKeys;

		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);
		$id = $client->listDomains()->domains[0]->ID;
		$domain = $client->getDomain($id);

		$this->assertNotEmpty($domain->name);
	}

	function testClientCanCreateDomain() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);

		$domainName = 'test-php-create-' . $tk->WRITE_TEST_DOMAIN_NAME;

		$domain = $client->createDomain($domainName);

		$this->assertNotEmpty($domain->ID);
	}

	function testClientCanEditDomain() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);
    
		$domainName = 'test-php-edit-' . $tk->WRITE_TEST_DOMAIN_NAME;
		$returnPath = 'return.' . $domainName;

		$domain = $client->createDomain($domainName, $returnPath);

		$updated = $client->editDomain($domain->ID, 'updated-' . $returnPath);

		$this->assertNotSame($domain->returnpathdomain, $updated->returnpathdomain);
	}

	function testClientCanDeleteDomain() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);
    
		$domainName = $tk->WRITE_TEST_DOMAIN_NAME;

		$name = 'test-php-delete-' . $domainName;
		$domain = $client->createDomain($name);

		$client->deleteDomain($domain->ID);

		$domains = $client->listDomains()->domains;

		foreach ($domains as $key => $value) {
			$this->assertNotSame($domain->name, $value->name);
		}
	}

	function testClientCanVerifyDKIM() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);

		$domains = $client->listDomains()->domains;
		foreach ($domains as $key => $value) {
			$verify = $client->verifyDKIM($value->ID);

			$this->assertSame($verify->ID, $value->ID);
			$this->assertSame($verify->Name, $value->Name);
		}
	}

	function testClientCanVerifyReturnPath() {
		$tk = parent::$testKeys;
		$client = new PostmarkAdminClient($tk->WRITE_ACCOUNT_TOKEN, $tk->TEST_TIMEOUT);

		$domains = $client->listDomains()->domains;
		foreach ($domains as $key => $value) {
			$verify = $client->verifyReturnPath($value->ID);

			$this->assertSame($verify->ID, $value->ID);
			$this->assertSame($verify->Name, $value->Name);
		}
	}

}

?>
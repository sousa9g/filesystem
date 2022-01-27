<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php53;

use Joomla\Filesystem\Patcher;
use Joomla\Filesystem\Path;
use PHPUnit\Framework\TestCase;

/**
 * A unit test class for Patcher
 *
 * @since  1.0
 */
class PatcherTest extends TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public static function setUpBeforeClass()
	{
		if (!\defined('JPATH_ROOT'))
		{
			self::markTestSkipped('Constant `JPATH_ROOT` is not defined.');
		}
	}

	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		// Make sure previous test files are cleaned up
		$this->_cleanupTestFiles();

		// Make some test files and folders
		mkdir(Path::clean(dirname(__FILE__) . '/tmp/patcher'), 0777, true);
	}

	/**
	 * Remove created files
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		$this->_cleanupTestFiles();
	}

	/**
	 * Convenience method to cleanup before and after test
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function _cleanupTestFiles()
	{
		$this->_cleanupFile(Path::clean(dirname(__FILE__) . '/tmp/patcher/lao2tzu.diff'));
		$this->_cleanupFile(Path::clean(dirname(__FILE__) . '/tmp/patcher/lao'));
		$this->_cleanupFile(Path::clean(dirname(__FILE__) . '/tmp/patcher/tzu'));
		$this->_cleanupFile(Path::clean(dirname(__FILE__) . '/tmp/patcher'));
	}

	/**
	 * Convenience method to clean up for files test
	 *
	 * @param   string  $path  The path to clean
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function _cleanupFile($path)
	{
		if (file_exists($path))
		{
			if (is_file($path))
			{
				unlink($path);
			}
			elseif (is_dir($path))
			{
				rmdir($path);
			}
		}
	}

	/**
	 * Data provider for testAdd
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function addData()
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';

		// Use of realpath to ensure test works for on all platforms
		return array(
			array(
				$udiff,
				realpath(dirname(__FILE__) . '/tmp/patcher'),
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => realpath(dirname(__FILE__) . '/tmp/patcher') . DIRECTORY_SEPARATOR,
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				realpath(dirname(__FILE__) . '/tmp/patcher') . DIRECTORY_SEPARATOR,
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => realpath(dirname(__FILE__) . '/tmp/patcher') . DIRECTORY_SEPARATOR,
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				null,
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => '',
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				'',
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => DIRECTORY_SEPARATOR,
						'strip' => 0
					)
				)
			),
		);
	}

	/**
	 * Test Patcher::add add a unified diff string to the patcher
	 *
	 * @param   string  $udiff     Unified diff input string
	 * @param   string  $root      The files root path
	 * @param   string  $strip     The number of '/' to strip
	 * @param   array   $expected  The expected array patches
	 *
	 * @return  void
	 *
	 * @dataProvider addData
	 * @since   1.0
	 */
	public function testAdd($udiff, $root, $strip, $expected)
	{
		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, $root, $strip);
		$this->assertAttributeEquals(
			$expected,
			'patches',
			$patcher,
			'Line:' . __LINE__ . ' The patcher cannot add the unified diff string.'
		);
	}

	/**
	 * Test Patcher::addFile add a unified diff file to the patcher
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddFile()
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';

		// Use of realpath to ensure test works for on all platforms
		file_put_contents(dirname(__FILE__) . '/tmp/patcher/lao2tzu.diff', $udiff);
		$patcher = Patcher::getInstance()->reset();
		$patcher->addFile(dirname(__FILE__) . '/tmp/patcher/lao2tzu.diff', realpath(dirname(__FILE__) . '/tmp/patcher'));

		$this->assertAttributeEquals(
			array(
				array(
					'udiff' => $udiff,
					'root' => realpath(dirname(__FILE__) . '/tmp/patcher') . DIRECTORY_SEPARATOR,
					'strip' => 0
				)
			),
			'patches',
			$patcher,
			'Line:' . __LINE__ . ' The patcher cannot add the unified diff file.'
		);
	}

	/**
	 * Patcher::reset reset the patcher to its initial state
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testReset()
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';
		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, dirname(__FILE__) . '/patcher/');
		$this->assertEquals(
			$patcher->reset(),
			$patcher,
			'Line:' . __LINE__ . ' The reset method does not return $this for chaining.'
		);
		$this->assertAttributeEquals(
			array(),
			'sources',
			$patcher,
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertAttributeEquals(
			array(),
			'destinations',
			$patcher,
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertAttributeEquals(
			array(),
			'removals',
			$patcher,
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertAttributeEquals(
			array(),
			'patches',
			$patcher,
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
	}

	/**
	 * Data provider for testApply
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function applyData()
	{
		return array(
			// Test classical feature
			'Test classical feature' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(
					dirname(__FILE__) . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test truncated hunk
			'Test truncated hunk' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1 +1 @@
-The Way that can be told of is not the eternal Way;
+The named is the mother of all things.
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(
					dirname(__FILE__) . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The named is the mother of all things.
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				1,
				false
			),

			// Test strip is null
			'Test strip is null' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				null,
				array(
					dirname(__FILE__) . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test strip is different of 0
			'Test strip is different of 0' => array(
				'Index: lao
===================================================================
--- /path/to/lao	2011-09-21 16:05:45.086909120 +0200
+++ /path/to/tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				3,
				array(
					dirname(__FILE__) . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test create file
			'Test create file' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -0,0 +1,14 @@
+The Nameless is the origin of Heaven and Earth;
+The named is the mother of all things.
+
+Therefore let there always be non-being,
+  so we may see their subtlety,
+And let there always be being,
+  so we may see their outcome.
+The two are the same,
+But after they are produced,
+  they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
+
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test patch itself
			'Test patch itself' => array(
				'Index: lao
===================================================================
--- tzu	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test delete
			'Test delete' => array(
				'Index: lao
===================================================================
--- tzu	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,0 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
-Therefore let there always be non-being,
-  so we may see their subtlety,
-And let there always be being,
-  so we may see their outcome.
-The two are the same,
-But after they are produced,
-  they have different names.
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					dirname(__FILE__) . '/tmp/patcher/tzu' => null
				),
				1,
				false
			),

			// Test unexpected eof after header
			'Test unexpected eof after header 1' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected eof after header
			'Test unexpected eof after header 2' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected eof in header
			'Test unexpected eof in header' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test invalid diff in header
			'Test invalid diff in header' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected eof after hunk 1
			'Test unexpected eof after hunk 1' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,0 @@',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected eof after hunk 2
			'Test unexpected eof after hunk 2' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,11 @@
+The Way that can be told of is not the eternal Way;
+The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected remove line
			'Test unexpected remove line' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,1 +1,1 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
+The Nameless is the origin of Heaven and Earth;
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexpected add line
			'Test unexpected add line' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,1 +1,1 @@
+The Way that can be told of is not the eternal Way;
+The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test unexisting source
			'Test unexisting source' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(),
				array(),
				1,
				'RuntimeException'
			),

			// Test failed verify
			'Test failed verify' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				dirname(__FILE__) . '/tmp/patcher',
				0,
				array(
					dirname(__FILE__) . '/tmp/patcher/lao' => ''
				),
				array(),
				1,
				'RuntimeException'
			),
		);
	}

	/**
	 * Patcher::apply apply the patches
	 *
	 * @param   string   $udiff         Unified diff input string
	 * @param   string   $root          The files root path
	 * @param   string   $strip         The number of '/' to strip
	 * @param   array    $sources       The source files
	 * @param   array    $destinations  The destinations files
	 * @param   integer  $result        The number of files patched
	 * @param   mixed    $throw         The exception throw, false for no exception
	 *
	 * @return  void
	 *
	 * @dataProvider applyData
	 * @since   1.0
	 */
	public function testApply($udiff, $root, $strip, $sources, $destinations, $result, $throw)
	{
		if ($throw)
		{
			// expectException was added in PHPUnit 5.2 and setExpectedException removed in 6.0
			if (method_exists($this, 'expectException'))
			{
				$this->expectException($throw);
			}
			else
			{
				$this->setExpectedException($throw);
			}
		}

		foreach ($sources as $path => $content)
		{
			file_put_contents($path, $content);
		}

		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, $root, $strip);
		$this->assertEquals(
			$result,
			$patcher->apply(),
			'Line:' . __LINE__ . ' The patcher did not patch ' . $result . ' file(s).'
		);

		foreach ($destinations as $path => $content)
		{
			if (\is_null($content))
			{
				$this->assertFalse(
					is_file($path),
					'Line:' . __LINE__ . ' The patcher did not succeed in patching ' . $path
				);
			}
			else
			{
				// Remove all vertical characters to ensure system independent compare
				$content = preg_replace('/\v/', '', $content);
				$data = file_get_contents($path);
				$data = preg_replace('/\v/', '', $data);

				$this->assertEquals(
					$content,
					$data,
					'Line:' . __LINE__ . ' The patcher did not succeed in patching ' . $path
				);
			}
		}
	}
}

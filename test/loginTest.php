<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class LoginPhpTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset superglobals and session before each test
        $_POST = [];
        $_SESSION = [];
        $_SERVER = [];
        // Remove headers set in previous tests
        if (function_exists('xdebug_get_headers')) {
            foreach (xdebug_get_headers() as $header) {
                header_remove(explode(':', $header, 2)[0]);
            }
        }
    }

    public function test_successful_login_and_role_based_redirection()
    {
        // Simulate POST request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'correct_password';
        $_SESSION = [];

        // Mock PDO and dependencies
        $userData = [
            'user_id' => 1,
            'username' => 'testuser',
            'password' => password_hash('correct_password', PASSWORD_DEFAULT),
            'role' => 'admin'
        ];

        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('setAttribute')
            ->with(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT user_id, username, password, role FROM users WHERE username = :username'))
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->with(['username' => 'testuser'])
            ->willReturn(true);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($userData);

        // Patch PDO and password_verify
        $this->mockGlobalPDO($pdoMock);
        $this->mockPasswordVerify(true);

        // Capture headers and output
        $this->expectOutputRegex('/^$/'); // No output expected

        // Patch http_response_code and header
        $httpCodeSet = null;
        $headerSet = null;
        $this->mockHttpResponseCode(function($code) use (&$httpCodeSet) { $httpCodeSet = $code; });
        $this->mockHeader(function($header) use (&$headerSet) { $headerSet = $header; });

        // Run the login script
        include __DIR__ . '/../backend/login.php';

        // Assert session is set
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('testuser', $_SESSION['username']);
        $this->assertEquals('admin', $_SESSION['role']);

        // Assert redirection and http code
        $this->assertEquals(200, $httpCodeSet);
        $this->assertEquals('Location: ../front/admin.php', $headerSet);
    }

    public function test_prevent_login_when_already_logged_in()
    {
        $_SESSION['user_id'] = 123;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Capture output (should be JS alert and redirect)
        ob_start();
        include __DIR__ . '/../backend/login.php';
        $output = ob_get_clean();

        $this->assertStringContainsString("alert('Már be vagy jelentkezve!')", $output);
        $this->assertStringContainsString("window.location.href = '../front/index.php';", $output);
    }

    public function test_http_response_code_on_successful_login()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'correct_password';
        $_SESSION = [];

        $userData = [
            'user_id' => 2,
            'username' => 'testuser',
            'password' => password_hash('correct_password', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('setAttribute')
            ->with(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->with(['username' => 'testuser'])
            ->willReturn(true);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($userData);

        $this->mockGlobalPDO($pdoMock);
        $this->mockPasswordVerify(true);

        $httpCodeSet = null;
        $headerSet = null;
        $this->mockHttpResponseCode(function($code) use (&$httpCodeSet) { $httpCodeSet = $code; });
        $this->mockHeader(function($header) use (&$headerSet) { $headerSet = $header; });

        $this->expectOutputRegex('/^$/');

        include __DIR__ . '/../backend/login.php';

        $this->assertEquals(200, $httpCodeSet);
        $this->assertEquals('Location: ../front/index.php?login=success', $headerSet);
    }

    public function test_login_failure_with_incorrect_password()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'wrong_password';
        $_SESSION = [];

        $userData = [
            'user_id' => 3,
            'username' => 'testuser',
            'password' => password_hash('correct_password', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('setAttribute')
            ->with(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->with(['username' => 'testuser'])
            ->willReturn(true);

        $stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($userData);

        $this->mockGlobalPDO($pdoMock);
        $this->mockPasswordVerify(false);

        ob_start();
        include __DIR__ . '/../backend/login.php';
        $output = ob_get_clean();

        $this->assertStringContainsString("alert('Hibás felhasználónév vagy jelszó!')", $output);
        $this->assertStringContainsString("window.location.href = '../front/login.php';", $output);
    }

    public function test_database_connection_error_handling()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'any_password';
        $_SESSION = [];

        // Simulate PDO constructor throwing exception
        $this->mockGlobalPDOException();

        ob_start();
        include __DIR__ . '/../backend/login.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Database error:', $output);
    }

    public function test_missing_or_empty_post_parameters()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = []; // No username or password
        $_SESSION = [];

        // Mock PDO so it is never called
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->never())->method('prepare');
        $this->mockGlobalPDO($pdoMock);

        ob_start();
        include __DIR__ . '/../backend/login.php';
        $output = ob_get_clean();

        // Should not throw error, should show alert for invalid credentials
        $this->assertStringContainsString("alert('Hibás felhasználónév vagy jelszó!')", $output);
    }

    // --- Helpers for patching/mocking ---

    private function mockGlobalPDO($pdoMock)
    {
        // Patch PDO global constructor
        $GLOBALS['__PHPUNIT_PDO_MOCK'] = $pdoMock;
        eval('
            namespace {
                if (!function_exists("__phpunit_pdo_ctor")) {
                    function __phpunit_pdo_ctor($dsn, $username = null, $password = null, $options = null) {
                        return $GLOBALS["__PHPUNIT_PDO_MOCK"];
                    }
                    class_alias("PDO", "__PHPUNIT_ORIG_PDO");
                    class PDO extends \__PHPUNIT_ORIG_PDO {
                        public function __construct($dsn, $username = null, $password = null, $options = null) {
                            $pdo = \__phpunit_pdo_ctor($dsn, $username, $password, $options);
                            foreach (get_class_methods($pdo) as $method) {
                                $this->$method = $pdo->$method;
                            }
                        }
                    }
                }
            }
        ');
    }

    private function mockGlobalPDOException()
    {
        eval('
            namespace {
                if (!function_exists("__phpunit_pdo_ctor_exception")) {
                    function __phpunit_pdo_ctor_exception($dsn, $username = null, $password = null, $options = null) {
                        throw new \PDOException("Simulated connection error");
                    }
                    class_alias("PDO", "__PHPUNIT_ORIG_PDO");
                    class PDO extends \__PHPUNIT_ORIG_PDO {
                        public function __construct($dsn, $username = null, $password = null, $options = null) {
                            \__phpunit_pdo_ctor_exception($dsn, $username, $password, $options);
                        }
                    }
                }
            }
        ');
    }

    private function mockPasswordVerify($returnValue)
    {
        // Patch password_verify globally
        $GLOBALS['__PHPUNIT_PASSWORD_VERIFY'] = $returnValue;
        eval('
            namespace {
                if (!function_exists("password_verify")) {
                    function password_verify($password, $hash) {
                        return $GLOBALS["__PHPUNIT_PASSWORD_VERIFY"];
                    }
                }
            }
        ');
    }

    private function mockHttpResponseCode($callback)
    {
        // Patch http_response_code globally
        $GLOBALS['__PHPUNIT_HTTP_RESPONSE_CODE'] = $callback;
        eval('
            namespace {
                if (!function_exists("http_response_code")) {
                    function http_response_code($code = 0) {
                        $cb = $GLOBALS["__PHPUNIT_HTTP_RESPONSE_CODE"];
                        $cb($code);
                        return $code;
                    }
                }
            }
        ');
    }

    private function mockHeader($callback)
    {
        // Patch header globally
        $GLOBALS['__PHPUNIT_HEADER'] = $callback;
        eval('
            namespace {
                if (!function_exists("header")) {
                    function header($header, $replace = true, $response_code = 0) {
                        $cb = $GLOBALS["__PHPUNIT_HEADER"];
                        $cb($header);
                    }
                }
            }
        ');
    }
}
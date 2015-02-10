<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * For more information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace JNB\DoctrineDBALBridge\Tests\MessageBus;

use JNB\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;
use SimpleBus\Message\Message;

/**
 * Test WrapsMessageHandlingInTransaction
 *
 * @license https://github.com/jaspernbrouwer/DoctrineDBALBridge/blob/master/LICENSE MIT
 * @author  Jasper N. Brouwer
 */
class WrapsMessageHandlingInTransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itWrapsTheNextMiddlewareInATransaction()
    {
        $nextIsCalled = false;
        $message      = $this->getMockBuilder('SimpleBus\Message\Message')->getMock();

        $nextMiddlewareCallable = function (Message $actualMessage) use ($message, &$nextIsCalled) {
            $this->assertSame($message, $actualMessage);
            $nextIsCalled = true;
        };

        $connection = $this->getMockBuilder('Doctrine\DBAL\Driver\Connection')->getMock();
        $connection
            ->expects($this->once())
            ->method('beginTransaction');
        $connection
            ->expects($this->once())
            ->method('commit');

        $middleware = new WrapsMessageHandlingInTransaction($connection);

        $middleware->handle($message, $nextMiddlewareCallable);

        $this->assertTrue($nextIsCalled);
    }

    /**
     * @test
     */
    public function itRollsTheTransactionBackWhenAnExceptionOccurs()
    {
        $exception = new \Exception();
        $message   = $this->getMockBuilder('SimpleBus\Message\Message')->getMock();

        $nextMiddlewareCallable = function () use ($exception) {
            throw $exception;
        };

        $connection = $this->getMockBuilder('Doctrine\DBAL\Driver\Connection')->getMock();
        $connection
            ->expects($this->once())
            ->method('beginTransaction');
        $connection
            ->expects($this->once())
            ->method('rollback');

        $middleware = new WrapsMessageHandlingInTransaction($connection);

        try {
            $middleware->handle($message, $nextMiddlewareCallable);

            $this->fail('An exception should have been thrown');
        } catch (\Exception $actualException) {
            $this->assertSame($exception, $actualException);
        }
    }
}

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

namespace JNB\DoctrineDBALBridge\CommandBus;

/**
 * Test WrapsNextCommandInTransaction
 *
 * @license https://github.com/jaspernbrouwer/DoctrineDBALBridge/blob/master/LICENSE MIT
 * @author  Jasper N. Brouwer
 */
class WrapsNextCommandInTransactionTest extends \PHPUnit_Framework_TestCase
{
    /* Subject */
    private $transactionalCommandBus;

    /* Collaborators */
    private $command;
    private $commandBus;
    private $connection;

    public function setUp()
    {
        $this->command = $this->getMockBuilder('SimpleBus\Command\Command')->getMock();

        $this->commandBus = $this->getMockBuilder('SimpleBus\Command\Bus\CommandBus')->getMock();
        $this->commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($this->command));

        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Driver\Connection')->getMock();
        $this->connection
            ->expects($this->once())
            ->method('beginTransaction');

        $this->transactionalCommandBus = new WrapsNextCommandInTransaction($this->connection);
        $this->transactionalCommandBus->setNext($this->commandBus);
    }

    /**
     * @test
     */
    public function itWrapsTheNextCommandInATransaction()
    {
        $this->connection
            ->expects($this->once())
            ->method('commit');

        $this->transactionalCommandBus->handle($this->command);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function itRollsTheTransactionBackWhenAnExceptionOccurs()
    {
        $this->commandBus
            ->method('handle')
            ->will($this->throwException(new \Exception()));

        $this->connection
            ->expects($this->once())
            ->method('rollback');

        $this->transactionalCommandBus->handle($this->command);
    }
}

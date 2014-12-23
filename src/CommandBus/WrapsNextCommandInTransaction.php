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

use Doctrine\DBAL\Driver\Connection;
use SimpleBus\Command\Bus\CommandBus;
use SimpleBus\Command\Bus\RemembersNext;
use SimpleBus\Command\Command;

/**
 * Class WrapsNextCommandInTransaction
 *
 * @license https://github.com/jaspernbrouwer/DoctrineDBALBridge/blob/master/LICENSE MIT
 * @author  Jasper N. Brouwer <jasper@nerdsweide.nl>
 */
class WrapsNextCommandInTransaction implements CommandBus
{
    use RemembersNext;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command)
    {
        $this->connection->beginTransaction();

        try {
            $this->next($command);

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollback();

            throw $e;
        }
    }
}

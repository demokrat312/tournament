<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Team\Controller;

use Team\Entity\Team;
use Team\Entity\TeamMatch;
use Team\Form\TeamForm;
use Team\Service\TeamManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;
use ZendDeveloperTools\Exception\ParameterMissingException;

/**
 * Class IndexController
 * @package Team\Controller
 * @method Request getRequest()
 * @method FlashMessenger flashMessenger
 */
class IndexController extends AbstractActionController
{
    /**
     * @var TeamManager
     */
    private $teamManager;

    /**
     * IndexController constructor.
     * @param TeamManager $teamManager
     */
    public function __construct(TeamManager $teamManager)
    {
        $this->teamManager = $teamManager;
    }

    /**
     * Отображает список всех команд
     *
     * @return ViewModel
     */
    public function teamListAction()
    {
        $teams            = $this->teamManager->getAllTeams();
        $tournamentStatus = $this->teamManager->getTournamentStatus();

        return new ViewModel([
            'teams'            => $teams,
            'tournamentStatus' => $tournamentStatus,
        ]);
    }

    /**
     * Сохранение команды
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function teamCreateAction()
    {
        // Получаем данные от клиента
        $request = $this->getRequest();
        $post    = $request->getPost()->toArray();

        // Создаем форму и задаем данные
        $form = new TeamForm($this->teamManager->getEntityManager());
        $form->setData($post);

        // Сохраняем данные, если пришла форма
        if ($request->isPost() && $form->isValid()) {
            $flashMessenger = $this->flashMessenger()->setNamespace('error');
            // Проверяем на нужное количество комманд
            if ($this->teamManager->isCanSplit()) {
                $flashMessenger->addMessage('Вы достигли максимального количества команд: ' . Team::TEAM_AMOUNT);
            } else {
                // Сохраняем
                $isSave = $this->teamManager->saveTeam($form->getObject());

                if ($isSave) {
                    return $this->redirect()->toRoute('team', ['action' => 'teamList']);
                } else {
                    $flashMessenger->addMessage('Не удалось сохранить');
                }

            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * Сортируем команды на дивизионы(Группы)
     */
    public function splitTeamsAction()
    {
        $isCan          = $this->teamManager->isCanSplit();
        $flashMessenger = $this->flashMessenger()->setNamespace('error');

        // Проверяем на нужное количество команд(16)
        if ($isCan) {
            $success = $this->teamManager->splitByGroup();
            if ($success === false) {
                $flashMessenger->addMessage('Не удалось разделить на группы');
            }
        } else {
            $flashMessenger->addMessage('Вы не создали необходимого количества команд.');
        }

        return $this->redirect()->toRoute('team', ['action' => 'teamList']);
    }

    /**
     * Кто с кем играет
     *
     * @return \Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function teamTossQualificationAction()
    {
        $this->teamManager->splitByMatchQualifying();

        return $this->redirect()->toRoute('team', ['action' => 'qualifying']);
    }

    /**
     * Кто с кем играет для playoff
     *
     * @return \Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function teamTossPlayoffAction()
    {
        $result = $this->teamManager->splitByMatchPlayoff();
        if ($result === false) {
            $this->flashMessenger()->setNamespace('error')
                 ->addMessage('Игра окончена');
        }

        return $this->redirect()->toRoute('team', ['action' => 'playoff']);
    }

    /**
     * Страница с результатами отборочных
     *
     * @return ViewModel
     */
    public function qualifyingAction()
    {
        $typeId = TeamMatch::TYPE_QUALIFYING;

        $matches          = $this->teamManager->getMatchesByType($typeId);
        $tournamentStatus = $this->teamManager->getTournamentStatus();

        $viewModel = new ViewModel([
            'matches'          => $matches,
            'tournamentStatus' => $tournamentStatus
        ]);

        return $viewModel;
    }

    /**
     * Страница с результатами playoff
     *
     * @return ViewModel
     */
    public function playoffAction()
    {
        $typeId = (int)$this->teamManager->getLastMatchTypeId();

        $matches      = [];
        $prevMatch    = [];
        $winTeam      = null;
        $currentTitle = '';

        // Если playoff начелься
        if ($this->teamManager->isPlayoff()) {
            $matches   = $this->teamManager->getMatchesByType($typeId);
            $prevMatch = $this->teamManager->getPrevPlayoffMatch($typeId);

            // Если последний этап берем команду которая победила
            if ($this->teamManager->getNextMatchTypeId($typeId) === null) {
                $winTeam = $matches[0]->getResult()->getTeamWin();
            }

            $currentTitle = TeamMatch::TYPE_TITLE[$typeId];
        }

        $tournamentStatus = $this->teamManager->getTournamentStatus();

        $viewModel = new ViewModel([
            'matches'          => $matches,
            'prevMatch'        => $prevMatch,
            'tournamentStatus' => $tournamentStatus,
            'winTeam'          => $winTeam,
            'currentTitle'     => $currentTitle,
        ]);

        return $viewModel;

    }

    /**
     * Задаем результат для матчей
     *
     * @return \Zend\Http\Response
     * @throws ParameterMissingException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function teamMatchResultAction()
    {
        $typeId = (int)$this->params()->fromQuery('type', 0);

        $action = $this->teamManager->generateMatchResult($typeId);

        return $this->redirect()->toRoute('team', ['action' => $action]);
    }

    /**
     * Очищаем базу. Кромер таблицы team
     *
     * @return \Zend\Http\Response
     */
    public function clearAction()
    {
        $this->teamManager->clearDB();

        return $this->redirect()->toRoute('team', ['action' => 'teamList']);
    }

    /**
     * Очищаем таблицу team
     *
     * @return \Zend\Http\Response
     */
    public function clearTeamAction()
    {
        $this->teamManager->clearTeam();

        return $this->redirect()->toRoute('team', ['action' => 'teamList']);
    }
}

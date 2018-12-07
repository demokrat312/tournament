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

    public function indexAction()
    {
        return new ViewModel();
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
     * Сортируем команду на дивизионы
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
     * Кто с кем играет
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

    public function qualifyingAction()
    {
        $typeId = TeamMatch::TYPE_QUALIFYING;
        $title  = 'Qualifying';

        $matches          = $this->teamManager->getMatchesByType($typeId);
        $tournamentStatus = $this->teamManager->getTournamentStatus();

        $viewModel = new ViewModel([
            'matches'          => $matches,
            'tournamentStatus' => $tournamentStatus,
            'title'            => $title,
        ]);

        return $viewModel;
    }

    public function playoffAction()
    {
        $typeId = (int)$this->teamManager->getLastPlayOffTypeId();

        if ($this->teamManager->getNextMatchTypeId($typeId) === null) {
            $this->flashMessenger()->setNamespace('error')
                 ->addMessage('Игра окончена');
        }
        $title = 'Playoff';
        if ($typeId === TeamMatch::TYPE_QUALIFYING) {
            $matches = [];
        } else {

            $matches = $this->teamManager->getMatchesByType($typeId);
        }

        $tournamentStatus = $this->teamManager->getTournamentStatus();

        $viewModel = new ViewModel([
            'matches'          => $matches,
            'tournamentStatus' => $tournamentStatus,
            'title'            => $title,
        ]);

        return $viewModel;

    }

    /**
     * Задаем результат для матчей
     *
     * @return \Zend\Http\Response
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function teamMatchResultAction()
    {
        $typeId = (int)$this->params()->fromQuery('type', null);

        $action = $this->teamManager->generateMatchResult($typeId);


        return $this->redirect()->toRoute('team', ['action' => $action]);
    }

    public function clearAction() {
        $this->teamManager->clearDB();

        return $this->redirect()->toRoute('team', ['action' => 'teamList']);
    }

    public function clearTeamAction() {
        $this->teamManager->clearDB();

        return $this->redirect()->toRoute('team', ['action' => 'teamList']);
    }
}

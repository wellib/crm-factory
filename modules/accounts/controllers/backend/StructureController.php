<?php

namespace app\modules\accounts\controllers\backend;

use app\exceptions\ModelErrorException;
use app\modules\accounts\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class StructureController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $users = User::find()->all();
        $usersNew = [];
        foreach ($users as $user) {
            $usersNew[] = [
                'title' => $user->name . ($user->position ? " ($user->position)" : ''),
                'id' => (string)$user->id,
                'parent_id' => (string)$user->parent_id,
                'expanded' => true,
                'data' => [
                    'id' => (string)$user->id,
                ],
            ];
        }

        $treeUsers = $this->createTree($usersNew);

        return $this->render('index', [
            'users' => $users,
            'treeUsers' => $treeUsers,
        ]);
    }

    protected function createTree(array $elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->createTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function actionUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $nodes = Yii::$app->request->post('nodes');

        foreach ($nodes as $node) {
            $rows = $this->treeToArray($node);
            foreach ($rows as $row) {
                $user = User::findOne($row['id']);
                $user->setScenario(User::SCENARIO_UPDATE);
                $user->parent_id = $row['parent_id'];
                if (!$user->save()) {
                    throw new ModelErrorException($user);
                }
            }
        }

        return ['success' => true];
    }

    protected function treeToArray($node, $parentId = null)
    {
        $rows = [];
        $id = ArrayHelper::getValue($node, 'data.id');
        $children = ArrayHelper::getValue($node, 'children', []);
        foreach ($children as $child) {
            $rowsChild = $this->treeToArray($child, $id);
            $rows = array_merge($rows, $rowsChild);
        }

        $row = [
            'id' => $id,
            'parent_id' => $parentId,
        ];

        $rows[] = $row;

        return $rows;
    }
}

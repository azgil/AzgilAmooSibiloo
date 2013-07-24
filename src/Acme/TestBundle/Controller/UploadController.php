<?php

namespace Acme\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\TestBundle\Dependency\Task;
use Acme\TestBundle\Dependency\Posting;

class UploadController extends Controller {

    public function indexAction() {
        $name = 'omid';
        $message = \Swift_Message::newInstance()
        ->setSubject('swiff maillero ra endakhtam')
        ->setFrom('send@example.com')
        ->setTo('omid.shj@gmail.com')
        ->setBody(
            $this->renderView(
                'AcmeTestBundle:Default:index.txt.twig',
                array('name' => $name)
            )
        )
    ;
    $this->get('mailer')->send($message);
        return $this->render('AcmeTestBundle:Default:upload.html.twig', array('name' => '$name'));
    }

    public function editAction() {
        $posting = new Posting();
        $task = new Task();
        $task->setDueDate(new \DateTime('tomorrow'));
        $form = $this->createFormBuilder($task)->getForm();
        //->add('task', 'text')
        //->add('dueDate', 'date')

        $request = $this->getRequest();

        $editId = $request->get('editId');
        if (!preg_match('/^\d+$/', $editId)) {
            $editId = sprintf('%09d', mt_rand(0, 1999999999));
        }

        $fileUploader = $this->get('punk_ave.file_uploader');
        $fileUploader->mySyncFiles(
                array('from_folder' => '/tmp/attachments/' . $editId,
                    'to_folder' => '/attachments/' . $posting->getId(),
                    'remove_from_folder' => true,
                    'create_to_folder' => true));

        $files = $fileUploader->getFiles(array('folder' => 'attachments/' . $posting->getId()));
        $existingFiles = $this->get('punk_ave.file_uploader')->getFiles(array('folder' => 'tmp/attachments/' . $editId));

        if ($files) {
            $isNew = FALSE;
        } else {
            $isNew = TRUE;
        }

        return $this->render('AcmeTestBundle:Default:edit.html.twig', array('name' => 'shayan karami',
                    'posting' => $posting,
                    'editId' => $editId,
                    'form' => $form->createView(),
                    'isNew' => $isNew,
                    'cancel' => 'http://amoosibiloo.com/app_dev.php/testupload/edit',
                    'existingFiles' => $existingFiles,
                    'files' => $files));
    }

    public function uploadAction() {
        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId)) {
            throw new Exception("Bad edit id");
        }

        $this->get('punk_ave.file_uploader')->handleFileUpload(array('folder' => 'tmp/attachments/' . $editId));
    }

    public function cancleAction() {
        $editId = $this->getRequest()->get('editId');
        $this->get('punk_ave.file_uploader')->removeFiles(array('folder' => 'tmp/attachments/' . $editId));
        return $this->redirect($this->generateUrl('acme_test_upload_edit'));
    }

    public function deleteAction() {
        $id = $this->getRequest()->get('id');
        $this->get('punk_ave.file_uploader')->removeFiles(array('folder' => 'attachments/' . $id));
        return $this->redirect($this->generateUrl('acme_test_upload_edit'));
    }

}

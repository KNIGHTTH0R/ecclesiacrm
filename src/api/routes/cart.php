<?php

use Slim\Http\Request;
use Slim\Http\Response;

use EcclesiaCRM\Person2group2roleP2g2r;
use EcclesiaCRM\GroupQuery;
use EcclesiaCRM\Group;
use EcclesiaCRM\dto\Cart;
use EcclesiaCRM\SessionUser;

$app->group('/cart', function () {
  
    $this->get('/', 'getAllPeopleInCart' );
    $this->post('/', 'cartOperation' );
    $this->post('/emptyToGroup', 'emptyCartToGroup' );
    $this->post('/emptyToEvent', 'emptyCartToEvent' );
    $this->post('/emptyToNewGroup', 'emptyCartToNewGroup' );
    $this->post('/removeGroup', 'removeGroupFromCart' );
    $this->post('/removeStudentGroup', 'removeStudentsGroupFromCart' );
    $this->post('/removeTeacherGroup', 'removeTeachersGroupFromCart' );
    $this->post('/delete', 'deletePersonCart' );

    /**
     * delete. This will empty the cart
     */
    $this->delete('/', 'removePersonCart' );

});

function getAllPeopleInCart (Request $request, Response $response, array $args) {
  return $response->withJSON(['PeopleCart' =>  $_SESSION['aPeopleCart']]);
}

function cartOperation ($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled() || SessionUser::getUser()->isAddRecordsEnabled())) {
        return $response->withStatus(401);
    }

      $cartPayload = (object)$request->getParsedBody();
      
      if ( isset ($cartPayload->Persons) && count($cartPayload->Persons) > 0 )
      {
        Cart::AddPersonArray($cartPayload->Persons);
      }
      elseif ( isset ($cartPayload->Family) )
      {
        Cart::AddFamily($cartPayload->Family);
      }
      elseif ( isset ($cartPayload->Group) )
      {
        Cart::AddGroup($cartPayload->Group);
      }
      elseif ( isset ($cartPayload->removeFamily) )
      {
        Cart::RemoveFamily($cartPayload->removeFamily);
      }
      elseif ( isset ($cartPayload->studentGroup) )
      {
        Cart::AddStudents($cartPayload->studentGroup);
      }
      elseif ( isset ($cartPayload->teacherGroup) )
      {
        Cart::AddTeachers($cartPayload->teacherGroup);
      }          
      else
      {
        throw new \Exception(gettext("POST to cart requires a Persons array, FamilyID, or GroupID"),500);
      }
      return $response->withJson(['status' => "success"]);
  }

function emptyCartToGroup ($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled() || SessionUser::getUser()->isAddRecordsEnabled())) {
        return $response->withStatus(401);
    }

    $cartPayload = (object)$request->getParsedBody();
    Cart::EmptyToGroup($cartPayload->groupID, $cartPayload->groupRoleID);
    return $response->withJson([
        'status' => "success",
        'message' => $iCount.' '.gettext('records(s) successfully added to selected Group.')
    ]);
}

function emptyCartToEvent ($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled() || SessionUser::getUser()->isAddRecordsEnabled())) {
        return $response->withStatus(401);
    }

    $cartPayload = (object)$request->getParsedBody();
    Cart::EmptyToEvent($cartPayload->eventID);
    return $response->withJson([
        'status' => "success",
        'message' => $iCount.' '.gettext('records(s) successfully added to selected Group.')
    ]);
}

function emptyCartToNewGroup ($request, $response, $args) {
    if (!SessionUser::getUser()->isAdmin() && !SessionUser::getUser()->isManageGroupsEnabled()) {
        return $response->withStatus(401);
    }
    
    $cartPayload = (object)$request->getParsedBody();
    $group = new Group();
    $group->setName($cartPayload->groupName);
    $group->save();
    
    Cart::EmptyToNewGroup($group->getId());
    
    echo $group->toJSON();
}

function removeGroupFromCart($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled())) {
        return $response->withStatus(401);
    }

    $cartPayload = (object)$request->getParsedBody();
    Cart::RemoveGroup($cartPayload->Group);
    return $response->withJson([
        'status' => "success",
        'message' => $iCount.' '.gettext('records(s) successfully deleted from the selected Group.')
    ]);
}

function removeStudentsGroupFromCart ($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled())) {
        return $response->withStatus(401);
    }

    $cartPayload = (object)$request->getParsedBody();
    Cart::RemoveStudents($cartPayload->Group);
    return $response->withJson([
        'status' => "success",
        'message' => $iCount.' '.gettext('records(s) successfully deleted from the selected Group.')
    ]);
}

function removeTeachersGroupFromCart ($request, $response, $args) {
    if (!(SessionUser::getUser()->isAdmin() || SessionUser::getUser()->isManageGroupsEnabled())) {
        return $response->withStatus(401);
    }

    $cartPayload = (object)$request->getParsedBody();
    Cart::RemoveTeachers($cartPayload->Group);
    return $response->withJson([
        'status' => "success",
        'message' => $iCount.' '.gettext('records(s) successfully deleted from the selected Group.')
    ]);
}

function deletePersonCart ($request, $response, $args) {
    if (!SessionUser::getUser()->isAdmin()) {
        return $response->withStatus(401);
    }
    
    $cartPayload = (object)$request->getParsedBody();
    if ( isset ($cartPayload->Persons) && count($cartPayload->Persons) > 0 )
    {
      Cart::DeletePersonArray($cartPayload->Persons);
    }
    else
    {
      $sMessage = gettext('Your cart is empty');
      if(sizeof($_SESSION['aPeopleCart'])>0) {
          Cart::DeletePersonArray ($_SESSION['aPeopleCart']);
          //$_SESSION['aPeopleCart'] = [];
      }
    }
    
    if (!empty($_SESSION['aPeopleCart'])) {
      $sMessage = gettext("You can't delete admin through the cart");
      $status = "failure";
    } else {
      $sMessage = gettext('Your cart and CRM has been successfully deleted');
      $status = "success";
    }
    
    return $response->withJson([
        'status' => $status,
        'message' => $sMessage
    ]);
}

function removePersonCart ($request, $response, $args) {
  
    $cartPayload = (object)$request->getParsedBody();
    if ( isset ($cartPayload->Persons) && count($cartPayload->Persons) > 0 )
    {
      Cart::RemovePersonArray($cartPayload->Persons);
    }
    else
    {
      $sMessage = gettext('Your cart is empty');
      if(sizeof($_SESSION['aPeopleCart'])>0) {
          $_SESSION['aPeopleCart'] = [];
          $sMessage = gettext('Your cart has been successfully emptied');
      }
    }
    return $response->withJson([
        'status' => "success",
        'message' =>$sMessage
    ]);

}
Feature: User profile page
  As an author
  I want a form to edit my user details
  So that I can update my contact details on the GigaDB web site
  
@ok
Scenario: View user profile
  Given I sign in as a user
  When I am on "/user/view_profile"
  Then I should see "Your profile page"
  And I should see "Personal details"
  And I should see "Email"
  And I should see "user@gigadb.org"
  And I should see "Last Name"
  And I should see "Smith"
  And I should see a check-box field "EditProfileForm_newsletter"
  And I should see "Add me to GigaDB's mailing list"
  And I should see a "Edit" button
  
@ok
Scenario: Ensure mailing list checkbox is not checkable
  Given I sign in as a user
  And I am on "/user/view_profile"
  And I should see "Your profile page"
  And I should see a check-box field "EditProfileForm_newsletter"
  And I should see "EditProfileForm[newsletter]" checkbox is not checked
  When I check "EditProfileForm[newsletter]" checkbox
  Then I should see "EditProfileForm[newsletter]" checkbox is not checked

@ok
Scenario: Ensure mailing list checkbox is checkable after clicking Edit button
  Given I sign in as a user
  And I am on "/user/view_profile"
  When I press the button "Edit"
  And I check "EditProfileForm[newsletter]" checkbox
  Then I should see "EditProfileForm[newsletter]" checkbox is checked

@ok
Scenario: Ensure mailing list checkbox remains checked when pressing Save button
  Given I sign in as a user
  When I am on "/user/view_profile"
  And I press the button "Edit"
  And I check "EditProfileForm[newsletter]" checkbox
  And I press the button "Save"
  Then I should see "EditProfileForm[newsletter]" checkbox is checked

//
//  PE_LoginViewController.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface PE_LoginViewController : UIViewController<UITableViewDelegate,UITableViewDataSource, UITextFieldDelegate>

    -(void)goToStartUpView:(id)sender;
    +(void)authenticateUserByFB:(id)sender;
    -(IBAction)logOut:(UIStoryboardSegue *)segue;

@end

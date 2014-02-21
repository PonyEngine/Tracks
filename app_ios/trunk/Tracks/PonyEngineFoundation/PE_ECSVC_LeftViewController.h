//
//  PE_ECSVC_LeftViewController.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/22/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol PE_ECSVC_LeftViewControllerDelegate;

@interface PE_ECSVC_LeftViewController : UIViewController
   @property (nonatomic, weak) id <PE_ECSVC_LeftViewControllerDelegate> delegate;
   @property (nonatomic, strong) NSArray *categoryList;
@end


@protocol PE_ECSVC_LeftViewControllerDelegate
   -(void)menuViewControllerDidFinishWithCategoryId:(NSInteger)categoryId;
@end


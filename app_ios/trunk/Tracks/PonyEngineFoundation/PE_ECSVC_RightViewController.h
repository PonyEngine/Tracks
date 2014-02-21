//
//  PE_ECSVC_RightViewController.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/22/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <UIKit/UIKit.h>
@protocol PE_ECSVC_RightViewControllerDelegate;

@interface PE_ECSVC_RightViewController : UIViewController
   @property (nonatomic, weak) id <PE_ECSVC_RightViewControllerDelegate> delegate;
   @property (nonatomic, strong) NSArray *categoryList;
@end


@protocol PE_ECSVC_RightViewControllerDelegate
   -(void)rightViewControllerDidFinishWithCategoryId:(NSInteger)categoryId;
@end


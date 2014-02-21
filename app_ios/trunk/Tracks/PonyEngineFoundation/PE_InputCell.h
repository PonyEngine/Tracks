//
//  PE_InputCell.h
//  VMote
//
//  Created by Savalas Colbert on 12/18/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface PE_InputCell : UITableViewCell {
	UITextField *inputTextField;
	
}
@property (nonatomic,retain) UITextField *inputTextField;
+ (id)cell;
+ (NSUInteger)neededHeightForDescription:(NSString *)description withTableWidth:(NSUInteger)tableWidth;
- (void)layoutSubviewsWithPlaceHolder:(NSString *)placeHolder isSecure:(bool)isSecure returnKeyType:(UIReturnKeyType)theReturnKeyType;
@end

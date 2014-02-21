//
//  PE_InputCell.m
//  VMote
//
//  Created by Savalas Colbert on 12/18/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "PE_InputCell.h"

@interface PE_InputCell ()

@end

@implementation PE_InputCell
@synthesize inputTextField;


+ (id)cell
{
	PE_InputCell *cell = [[PE_InputCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:@"PE_InputCell"];
    
	return cell;
}
- (void)layoutSubviewsWithPlaceHolder:(NSString *)placeHolder isSecure:(bool)isSecure returnKeyType:(UIReturnKeyType)theReturnKeyType
{
	/*[super layoutSubviews];
     int tablePadding = 40;
     int tableWidth = [[self superview] frame].size.width;
     if (tableWidth > 480) { // iPad
     tablePadding = 110;
     [[self textLabel] setFrame:CGRectMake(70,10,tableWidth-tablePadding-70,[[self class] neededHeightForDescription:[[self textLabel] text] withTableWidth:tableWidth])];
     } else {
     [[self textLabel] setFrame:CGRectMake(10,10,tableWidth-tablePadding,[[self class] neededHeightForDescription:[[self textLabel] text] withTableWidth:tableWidth])];
	 }*/
	// *inputTextField;
	inputTextField=[[UITextField alloc] initWithFrame:CGRectMake(20,10,270,30)];
	//inputTextField.backgroundColor=[UIColor orangeColor];
	inputTextField.placeholder=placeHolder;
	inputTextField.secureTextEntry=isSecure;
	inputTextField.keyboardType=UIKeyboardTypeASCIICapable;
	inputTextField.returnKeyType=theReturnKeyType;
	
	[self addSubview:inputTextField];
}

+ (NSUInteger)neededHeightForDescription:(NSString *)description withTableWidth:(NSUInteger)tableWidth
{
	/*int tablePadding = 20;
     int offset = 0;
     int textSize = 13;
     if (tableWidth > 480) { // iPad
     tablePadding = 110;
     offset = 70;
     textSize = 14;
     }
     CGSize labelSize = [description sizeWithFont:[UIFont systemFontOfSize:textSize] constrainedToSize:CGSizeMake(tableWidth-tablePadding-offset,1000) lineBreakMode:UILineBreakModeWordWrap];
     if (labelSize.height < 48) {
     return 58;
     }*/
	
	//return labelSize.height;
	return 40;
}

@end

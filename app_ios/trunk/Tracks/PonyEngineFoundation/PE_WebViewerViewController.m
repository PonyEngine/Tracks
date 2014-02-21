//
//  PE_WebViewerViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/21/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_WebViewerViewController.h"

@interface PE_WebViewerViewController ()
   @property(nonatomic,retain) IBOutlet UIWebView *webView;
   -(IBAction)cancel:(id)sender;
@end

@implementation PE_WebViewerViewController


- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
   self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
   if (self) {
      // Custom initialization
   }
   return self;
}

- (void)viewDidLoad
{
   [super viewDidLoad];
   
   //Create a URL object.
   NSURL *url = [NSURL URLWithString:self.uRLAddress];
   
   //URL Requst Object
   NSURLRequest *requestObj = [NSURLRequest requestWithURL:url cachePolicy:NSURLRequestReturnCacheDataElseLoad timeoutInterval:10.0];
   
   //Load the request in the UIWebView.
   [self.webView loadRequest:requestObj];
   
   //self.navigationItem.rightBarButtonItem=[[UIBarButtonItem alloc] initWithTitle:@"Cancel" style:UIBarButtonItemStyleBordered target:self action:@selector(cancel:)];
   
   self.navigationController.navigationBar.barStyle=UIBarStyleBlack;
}

- (void)didReceiveMemoryWarning
{
   [super didReceiveMemoryWarning];
   // Dispose of any resources that can be recreated.
}

-(IBAction)cancel:(id)sender{
   [self dismissViewControllerAnimated:YES completion:nil];
}


@end

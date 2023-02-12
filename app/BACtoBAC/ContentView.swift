//
//  ContentView.swift
//  BACtoBAC
//
//  Created by Justin Xia on 2/12/23.
//

import SwiftUI
import WebKit

extension View {
    func toAnyView() -> AnyView {
        AnyView(self)
    }
}
struct ContentView: View {
    
    @State private var showLoading: Bool = false
    
    var body: some View {
        VStack {
            WebView(url: URL(string: "https://bactobac.com")!)
        }
    }
}

struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView()
    }
}

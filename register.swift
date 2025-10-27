import SwiftUI
import UIKit

// MARK: - Camera Access View
struct CameraAccessView: View {
    let userID: Int
    let selectedAddress: String

    @State private var image: Image? = nil
    @State private var uiImage: UIImage? = nil
    @State private var showCamera = false
    @State private var showImagePicker = false
    @State private var navigateToHome = false
    @State private var isUploading = false

    var body: some View {
        NavigationStack {
            GeometryReader { geometry in
                ZStack {
                    LinearGradient(
                        gradient: Gradient(colors: [
                            Color(hex: "B7C9A9"),
                            Color(hex: "DAD5C3"),
                            Color(hex: "F0EFEA")
                        ]),
                        startPoint: .topLeading,
                        endPoint: .bottomTrailing
                    ).ignoresSafeArea()

                    ScrollView {
                        VStack(spacing: 40) {
                            Spacer().frame(height: 40)

                            Text("Address: \(selectedAddress)")
                                .font(.system(size: 16, design: .serif))
                                .foregroundColor(.black)
                                .multilineTextAlignment(.center)
                                .padding(.horizontal)

                            ZStack {
                                RoundedRectangle(cornerRadius: 16)
                                    .fill(Color.white)
                                    .frame(width: geometry.size.width * 0.9, height: geometry.size.height * 0.45)
                                    .shadow(color: .black.opacity(0.15), radius: 6, x: 0, y: 4)

                                if let image = image {
                                    image
                                        .resizable()
                                        .scaledToFill()
                                        .frame(width: geometry.size.width * 0.9, height: geometry.size.height * 0.45)
                                        .clipped()
                                        .cornerRadius(16)
                                } else {
                                    Text("Image Preview")
                                        .foregroundColor(.gray)
                                        .font(.system(size: 20, design: .serif))
                                }
                            }

                            HStack(spacing: 24) {
                                Button { showCamera = true } label: {
                                    Action2Item(imageName: "camera.fill", label: "CAMERA")
                                }

                                Button {
                                    image = nil
                                    uiImage = nil
                                } label: {
                                    Action2Item(imageName: "xmark.circle.fill", label: "CLEAR")
                                }

                                Button { showImagePicker = true } label: {
                                    Action2Item(imageName: "arrow.up.circle.fill", label: "UPLOAD")
                                }
                            }

                            if isUploading {
                                ProgressView("Uploading...")
                            }

                            Button("SUBMIT") {
                                uploadImage() // Store the data in the database when the button is clicked
                            }
                            .disabled(uiImage == nil)
                            .font(.system(size: 18, weight: .bold, design: .serif))
                            .foregroundColor(.white)
                            .padding(.horizontal, 60)
                            .padding(.vertical, 14)
                            .background(uiImage == nil ? Color.gray : Color.black)
                            .cornerRadius(12)
                        }
                        .padding(.bottom, 40)
                    }
                }
            }
            .navigationDestination(isPresented: $navigateToHome) {
                UserHomeWelcome(userID: userID)
            }
            .sheet(isPresented: $showCamera) {
                ImagePicker(sourceType: .camera) { (selected: UIImage?) in
                    if let selected = selected {
                        self.uiImage = selected
                        self.image = Image(uiImage: selected)
                    }
                }
            }
            .sheet(isPresented: $showImagePicker) {
                ImagePicker(sourceType: .photoLibrary) { (selected: UIImage?) in
                    if let selected = selected {
                        self.uiImage = selected
                        self.image = Image(uiImage: selected)
                    }
                }
            }
        }
    }

    func uploadImage() {
        guard let uiImage = uiImage,
              let imageData = uiImage.jpegData(compressionQuality: 0.8),
              let url = URL(string: "http://localhost/smartwastecontrol/upload_garbage.php") else {
            print("Invalid image or URL.")
            return
        }

        isUploading = true

        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        let boundary = UUID().uuidString
        request.setValue("multipart/form-data; boundary=\(boundary)", forHTTPHeaderField: "Content-Type")

        var body = Data()

        // Add user_id
        body.append("--\(boundary)\r\n".data(using: .utf8)!)
        body.append("Content-Disposition: form-data; name=\"user_id\"\r\n\r\n".data(using: .utf8)!)
        body.append("\(userID)\r\n".data(using: .utf8)!)

        // Add address
        body.append("--\(boundary)\r\n".data(using: .utf8)!)
        body.append("Content-Disposition: form-data; name=\"address\"\r\n\r\n".data(using: .utf8)!)
        body.append("\(selectedAddress)\r\n".data(using: .utf8)!)

        // Add image
        body.append("--\(boundary)\r\n".data(using: .utf8)!)
        body.append("Content-Disposition: form-data; name=\"image\"; filename=\"photo.jpg\"\r\n".data(using: .utf8)!)
        body.append("Content-Type: image/jpeg\r\n\r\n".data(using: .utf8)!)
        body.append(imageData)
        body.append("\r\n".data(using: .utf8)!)
        body.append("--\(boundary)--\r\n".data(using: .utf8)!)

        request.httpBody = body

        URLSession.shared.dataTask(with: request) { data, response, error in
            DispatchQueue.main.async {
                self.isUploading = false
            }

            if let error = error {
                print("Upload failed: \(error.localizedDescription)")
                return
            }

            guard let data = data else {
                print("Upload failed: No data received.")
                return
            }

            do {
                let response = try JSONDecoder().decode(Simple8Response.self, from: data)
                if response.success {
                    DispatchQueue.main.async {
                        self.navigateToHome = true
                    }
                } else {
                    print("Upload failed: \(response.message ?? "Unknown error")")
                }
            } catch {
                print("Decoding error: \(error)")
            }
        }.resume()
    }
}

// MARK: - Reusable Action Item Button View
struct Action2Item: View {
    let imageName: String
    let label: String

    var body: some View {
        VStack(spacing: 8) {
            Image(systemName: imageName)
                .resizable()
                .scaledToFit()
                .frame(width: 50, height: 50)
                .foregroundColor(.black)

            Text(label)
                .font(.system(size: 16, weight: .bold, design: .serif))
                .foregroundColor(.black)
        }
        .frame(width: 90, height: 110)
        .background(Color(hex: "E6EAE3"))
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.1), radius: 4)
    }
}

// MARK: - Image Picker Integration
struct ImagePicker: UIViewControllerRepresentable {
    var sourceType: UIImagePickerController.SourceType
    var completion: (UIImage?) -> Void

    func makeCoordinator() -> Coordinator {
        Coordinator(completion: completion)
    }

    func makeUIViewController(context: Context) -> UIImagePickerController {
        let picker = UIImagePickerController()
        picker.sourceType = sourceType
        picker.delegate = context.coordinator
        return picker
    }

    func updateUIViewController(_ uiViewController: UIImagePickerController, context: Context) {}

    class Coordinator: NSObject, UINavigationControllerDelegate, UIImagePickerControllerDelegate {
        var completion: (UIImage?) -> Void

        init(completion: @escaping (UIImage?) -> Void) {
            self.completion = completion
        }

        func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
            let image = info[.originalImage] as? UIImage
            completion(image)
            picker.dismiss(animated: true)
        }

        func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
            completion(nil)
            picker.dismiss(animated: true)
        }
    }
}

// MARK: - Response Model
struct Simple8Response: Codable {
    let success: Bool
    let message: String?
}


// MARK: - Preview
#Preview {
    CameraAccessView(userID: 1, selectedAddress: "Sample Address")
}

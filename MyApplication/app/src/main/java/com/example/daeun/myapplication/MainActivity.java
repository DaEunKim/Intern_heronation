package com.example.daeun.myapplication;

import android.nfc.Tag;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import com.loopj.android.http.*;

import cz.msebera.android.httpclient.Header;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        Button btn = (Button) findViewById(R.id.login);


        btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String ID = ((EditText) findViewById(R.id.id)).getText().toString();
                String PW = ((EditText) findViewById(R.id.pw)).getText().toString();


                String url = "http://heronation.net/test/dani/android.php";

                AsyncHttpClient client = new AsyncHttpClient();

                RequestParams params = new RequestParams();
                params.put("ID", ID);
                params.put("PW", PW);

                client.post (url, params, new TextHttpResponseHandler() {

                    @Override
                    public void onSuccess(int statusCode, Header[] headers, String response) {
                        //data 받아오기
                        System.out.println("response : "+response.toString());
                        Toast.makeText(getApplicationContext(), response.toString(), Toast.LENGTH_LONG).show();
                    }

                    @Override
                    public void onFailure(int statusCode, Header[] headers, String res, Throwable t) {
                        System.out.println("error");
                    }
                });
            }
        });
    }
}

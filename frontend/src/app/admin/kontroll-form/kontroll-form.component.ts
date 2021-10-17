import { Component } from '@angular/core';
import { FormGroup, FormControl } from '@angular/forms';

@Component({
  selector: 'brevet-kontroller-form',
  templateUrl: './kontroll-form.component.html',
  styleUrls: ['./kontroll-form.component.scss']
})
export class KontrollFormComponent {
  kontrollForm = new FormGroup({
    kontrollNamn: new FormControl(''),
    kontrollLatitud: new FormControl(''),
    kontrollLongitud: new FormControl(''),
  });
  onSubmit(){
    console.log('Sent')
    console.warn(this.kontrollForm.value)
  }
}
